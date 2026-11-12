import { NextResponse } from "next/server";
import { authConfig, cookieName, cookieOptions, flowAge, signCookie, startGoogleFlow } from "../../../../../auth/google";

export async function GET() {
  const config = authConfig();
  if (!config) return new Response("Login com Google indisponível.", { status: 503 });
  const { url, ...flow } = startGoogleFlow(config);
  const response = NextResponse.redirect(url);
  response.headers.set("Cache-Control", "no-store");
  response.cookies.set(cookieName(config, "flow"), await signCookie(config, "flow", flow, flowAge), cookieOptions(config, flowAge));
  return response;
}
