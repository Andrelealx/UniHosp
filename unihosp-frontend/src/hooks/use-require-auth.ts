"use client";

import { useEffect } from "react";
import { usePathname, useRouter } from "next/navigation";
import { useAuthStore } from "@/store/auth-store";

export function useRequireAuth() {
  const router = useRouter();
  const pathname = usePathname();
  const { token, hydrated } = useAuthStore();

  useEffect(() => {
    if (!hydrated) return;
    if (!token && !pathname.startsWith("/painel")) {
      router.replace("/login");
    }
  }, [hydrated, pathname, router, token]);

  return {
    token,
    hydrated,
    isAuthenticated: Boolean(token),
  };
}
