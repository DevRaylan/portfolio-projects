import { NextResponse } from "next/server";
import { authConfig, cookieName, cookieOptions } from "../../../../auth/google";

export async function POST(request: Request) {
  const config = authConfig();
  if (!config || request.headers.get("origin") !== config.origin) {
    return new Response("Solicitação inválida.", { status: 403 });
  }
  const response = NextResponse.redirect(new URL("/", config.origin), 303);
  response.headers.set("Cache-Control", "no-store");
  response.cookies.set(cookieName(config, "session"), "", cookieOptions(config, 0));
  response.cookies.set(cookieName(config, "flow"), "", cookieOptions(config, 0));
  return response;
}
