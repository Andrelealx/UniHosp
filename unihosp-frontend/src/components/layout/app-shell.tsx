"use client";

import { useState } from "react";
import { Sidebar } from "@/components/layout/sidebar";
import { Topbar } from "@/components/layout/topbar";
import { useRequireAuth } from "@/hooks/use-require-auth";

export function AppShell({ children }: { children: React.ReactNode }) {
  const [open, setOpen] = useState(false);
  const { hydrated, isAuthenticated } = useRequireAuth();

  if (!hydrated) {
    return <div className="grid min-h-screen place-items-center text-sm text-muted-foreground">Carregando sessão...</div>;
  }

  if (!isAuthenticated) return null;

  return (
    <div className="flex min-h-screen premium-grid">
      <Sidebar open={open} onClose={() => setOpen(false)} />
      <div className="min-w-0 flex-1">
        <Topbar onOpenMenu={() => setOpen(true)} />
        <main className="p-3 pb-8 md:p-5">{children}</main>
      </div>
    </div>
  );
}
