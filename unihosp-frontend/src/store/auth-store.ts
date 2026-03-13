"use client";

import { create } from "zustand";
import { createJSONStorage, persist } from "zustand/middleware";
import type { RoleName, User } from "@/types/api";

interface AuthState {
  token: string | null;
  user: User | null;
  hydrated: boolean;
  setSession: (payload: { token: string; user: User }) => void;
  clearSession: () => void;
  setHydrated: (value: boolean) => void;
  hasRole: (roles: RoleName | RoleName[]) => boolean;
}

function persistTokenCookie(token: string | null) {
  if (typeof document === "undefined") return;
  if (!token) {
    document.cookie = "unihosp_token=; path=/; max-age=0";
    return;
  }
  document.cookie = `unihosp_token=${token}; path=/; max-age=${60 * 60 * 24 * 7}; samesite=lax`;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      token: null,
      user: null,
      hydrated: false,
      setSession: ({ token, user }) => {
        persistTokenCookie(token);
        set({ token, user });
      },
      clearSession: () => {
        persistTokenCookie(null);
        set({ token: null, user: null });
      },
      setHydrated: (value) => set({ hydrated: value }),
      hasRole: (roles) => {
        const required = Array.isArray(roles) ? roles : [roles];
        return required.some((role) => get().user?.roles?.includes(role));
      },
    }),
    {
      name: "unihosp-auth",
      storage: createJSONStorage(() => localStorage),
      onRehydrateStorage: () => (state) => {
        state?.setHydrated(true);
        persistTokenCookie(state?.token ?? null);
      },
    },
  ),
);
