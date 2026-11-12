import { createHash, randomBytes, timingSafeEqual } from "node:crypto";
import { createRemoteJWKSet, jwtVerify, SignJWT } from "jose";
import { cookies } from "next/headers";

const googleKeys = createRemoteJWKSet(new URL("https://www.googleapis.com/oauth2/v3/certs"));
const issuer = "divide-ai";
export const sessionAge = 60 * 60 * 24 * 7;
export const flowAge = 60 * 10;

export type Account = { id: string; name: string; email: string };

export function authConfig() {
  const { APP_URL, GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, AUTH_SECRET } = process.env;
  if (!APP_URL || !GOOGLE_CLIENT_ID || !GOOGLE_CLIENT_SECRET || !AUTH_SECRET || Buffer.byteLength(AUTH_SECRET) < 32) {
    return null;
  }

  try {
    const url = new URL(APP_URL);
    if (url.href !== `${url.origin}/`) return null;
    if (url.protocol !== "https:" && !(url.protocol === "http:" && ["localhost", "127.0.0.1", "[::1]"].includes(url.hostname))) {
      return null;
    }
    return {
      origin: url.origin,
      clientId: GOOGLE_CLIENT_ID,
      clientSecret: GOOGLE_CLIENT_SECRET,
      secret: new TextEncoder().encode(AUTH_SECRET),
      secure: url.protocol === "https:",
      callback: new URL("/api/auth/google/callback", url).href,
    };
  } catch {
    return null;
  }
}

type Config = NonNullable<ReturnType<typeof authConfig>>;
type CookieKind = "session" | "flow";

export function cookieName(config: Config, kind: CookieKind) {
  return `${config.secure ? "__Host-" : ""}divide-ai-${kind}`;
}

export function cookieOptions(config: Config, maxAge: number) {
  return { httpOnly: true, secure: config.secure, sameSite: "lax" as const, path: "/", maxAge };
}

export function signCookie(config: Config, kind: CookieKind, data: Record<string, string>, maxAge: number) {
  return new SignJWT(data).setProtectedHeader({ alg: "HS256" }).setIssuer(issuer)
    .setAudience(kind).setIssuedAt().setExpirationTime(Math.floor(Date.now() / 1000) + maxAge).sign(config.secret);
}

export async function readCookie(config: Config, kind: CookieKind) {
  const token = (await cookies()).get(cookieName(config, kind))?.value;
  if (!token) return null;
  try {
    const { payload } = await jwtVerify(token, config.secret, { algorithms: ["HS256"], issuer, audience: kind, requiredClaims: ["exp"] });
    return payload;
  } catch {
    return null;
  }
}

export async function accountFromSession(config: Config): Promise<Account | null> {
  const account = await readCookie(config, "session");
  if (typeof account?.id !== "string" || typeof account.name !== "string" || typeof account.email !== "string") return null;
  return { id: account.id, name: account.name, email: account.email };
}

export function startGoogleFlow(config: Config) {
  const state = randomBytes(32).toString("base64url");
  const verifier = randomBytes(32).toString("base64url");
  const nonce = randomBytes(32).toString("base64url");
  const url = new URL("https://accounts.google.com/o/oauth2/v2/auth");
  url.search = new URLSearchParams({
    client_id: config.clientId,
    redirect_uri: config.callback,
    response_type: "code",
    scope: "openid email profile",
    state,
    nonce,
    code_challenge: createHash("sha256").update(verifier).digest("base64url"),
    code_challenge_method: "S256",
    prompt: "select_account",
  }).toString();
  return { url, state, verifier, nonce };
}

export function matchesState(received: string, expected: string) {
  const input = Buffer.from(received);
  const saved = Buffer.from(expected);
  return input.length === saved.length && timingSafeEqual(input, saved);
}

export async function exchangeGoogleCode(config: Config, code: string, verifier: string, nonce: string): Promise<Account> {
  const response = await fetch("https://oauth2.googleapis.com/token", {
    method: "POST",
    body: new URLSearchParams({
      code,
      client_id: config.clientId,
      client_secret: config.clientSecret,
      redirect_uri: config.callback,
      grant_type: "authorization_code",
      code_verifier: verifier,
    }),
    cache: "no-store",
    signal: AbortSignal.timeout(10_000),
  });

  if (!response.ok) throw new Error("Google recusou o código de autorização.");
  const tokens = await response.json();
  if (typeof tokens.id_token !== "string") throw new Error("O Google não retornou a identidade.");

  const { payload } = await jwtVerify(tokens.id_token, googleKeys, {
    algorithms: ["RS256"],
    audience: config.clientId,
    issuer: ["https://accounts.google.com", "accounts.google.com"],
    requiredClaims: ["exp", "iat", "sub", "nonce"],
  });
  if (payload.nonce !== nonce || (payload.azp !== undefined && payload.azp !== config.clientId)
    || (Array.isArray(payload.aud) && payload.aud.length > 1 && payload.azp !== config.clientId)
    || typeof payload.sub !== "string" || !payload.sub || (payload.email_verified !== true && payload.email_verified !== "true")
    || typeof payload.email !== "string" || !payload.email) {
    throw new Error("Não foi possível confirmar a identidade do Google.");
  }
  return { id: payload.sub, name: typeof payload.name === "string" && payload.name.trim() ? payload.name : payload.email, email: payload.email };
}
