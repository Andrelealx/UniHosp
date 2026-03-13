"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { LogOut, Moon, Sun, X } from "lucide-react";
import { useTheme } from "next-themes";
import { appNavigation } from "@/config/navigation";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { useAuthStore } from "@/store/auth-store";

interface SidebarProps {
  open: boolean;
  onClose: () => void;
}

export function Sidebar({ open, onClose }: SidebarProps) {
  const pathname = usePathname();
  const { theme, setTheme } = useTheme();
  const { user, clearSession } = useAuthStore();

  const content = (
    <div className="flex h-full flex-col">
      <div className="flex items-center justify-between border-b border-border px-4 py-4">
        <div>
          <p className="font-display text-lg font-semibold text-primary">UniHosp</p>
          <p className="text-xs text-muted-foreground">MVP Hospitalar</p>
        </div>
        <button className="rounded-lg p-1 text-muted-foreground md:hidden" onClick={onClose} type="button">
          <X className="h-5 w-5" />
        </button>
      </div>

      <div className="flex-1 overflow-y-auto px-3 py-4">
        <nav className="space-y-1">
          {appNavigation.map((item) => {
            const Icon = item.icon;
            const active = pathname === item.href || pathname.startsWith(`${item.href}/`);
            return (
              <Link
                key={item.href}
                href={item.href}
                onClick={onClose}
                className={cn(
                  "flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition-all",
                  active
                    ? "bg-primary text-primary-foreground shadow-sm"
                    : "text-muted-foreground hover:bg-muted hover:text-foreground",
                )}
              >
                <Icon className="h-4 w-4" />
                {item.label}
              </Link>
            );
          })}
        </nav>
      </div>

      <div className="border-t border-border p-3">
        <div className="mb-3 rounded-xl bg-muted/70 p-3">
          <p className="text-xs text-muted-foreground">Logado como</p>
          <p className="truncate text-sm font-semibold">{user?.name ?? "Usuário"}</p>
        </div>
        <div className="grid grid-cols-2 gap-2">
          <Button
            variant="outline"
            size="sm"
            onClick={() => setTheme(theme === "dark" ? "light" : "dark")}
            type="button"
          >
            {theme === "dark" ? <Sun className="h-4 w-4" /> : <Moon className="h-4 w-4" />}
            Tema
          </Button>
          <Button
            variant="ghost"
            size="sm"
            onClick={() => {
              clearSession();
              onClose();
            }}
            type="button"
          >
            <LogOut className="h-4 w-4" />
            Sair
          </Button>
        </div>
      </div>
    </div>
  );

  return (
    <>
      <aside className="surface hidden w-72 rounded-none border-r md:block">{content}</aside>
      <div
        className={cn(
          "fixed inset-0 z-40 bg-slate-950/45 transition-opacity md:hidden",
          open ? "pointer-events-auto opacity-100" : "pointer-events-none opacity-0",
        )}
        onClick={onClose}
      />
      <aside
        className={cn(
          "surface fixed inset-y-0 left-0 z-50 w-72 rounded-none border-r transition-transform md:hidden",
          open ? "translate-x-0" : "-translate-x-full",
        )}
      >
        {content}
      </aside>
    </>
  );
}
