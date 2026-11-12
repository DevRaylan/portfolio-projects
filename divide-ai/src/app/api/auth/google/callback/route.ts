import { NextResponse } from "next/server";
import { authConfig, cookieName, cookieOptions, exchangeGoogleCode, matchesState, readCookie, sessionAge, signCookie } from "../../../../../auth/google";

export async function GET(request: Request) {
  const config = authConfig();
  if (!config) return new Response("Login com Google indisponível.", { status: 503 });

  const { origin } = config;
  const flowCookie = cookieName(config, "flow");
  const clearFlow = cookieOptions(config, 0);
  const query = new URL(request.url).searchParams;
  const flow = await readCookie(config, "flow");
  const state = query.get("state");
  const code = query.get("code");

  function redirect(path: string) {
    const response = NextResponse.redirect(new URL(path, origin), 303);
    response.headers.set("Cache-Control", "no-store");
    response.cookies.set(flowCookie, "", clearFlow);
    return response;
  }

  if (!state || typeof flow?.state !== "string" || !matchesState(state, flow.state)
    || typeof flow.verifier !== "string" || typeof flow.nonce !== "string") {
    return redirect("/?login=expired");
  }
  if (query.has("error") || !code) return redirect("/?login=cancelled");

  try {
    const account = await exchangeGoogleCode(config, code, flow.verifier, flow.nonce);
    const response = redirect("/");
    response.cookies.set(cookieName(config, "session"), await signCookie(config, "session", account, sessionAge), cookieOptions(config, sessionAge));
    return response;
  } catch {
    return redirect("/?login=failed");
  }
}
