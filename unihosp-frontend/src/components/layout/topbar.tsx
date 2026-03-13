"use client";

import { Menu, Search } from "lucide-react";
import { usePathname } from "next/navigation";
import { Input } from "@/components/ui/input";

interface TopbarProps {
  onOpenMenu: () => void;
}

function pageTitle(pathname: string) {
  const map: Record<string, string> = {
    "/dashboard": "Dashboard Operacional",
    "/pacientes": "Pacientes",
    "/recepcao": "Recepção",
    "/filas": "Filas",
    "/senhas": "Senhas",
    "/chamadas": "Chamadas",
    "/triagem": "Triagem",
    "/atendimentos": "Atendimento Médico",
    "/prontuarios": "Prontuário",
    "/relatorios": "Relatórios",
    "/paineis": "Painéis TV",
    "/usuarios": "Usuários e Perfis",
  };

  return map[pathname] ?? "UniHosp";
}

export function Topbar({ onOpenMenu }: TopbarProps) {
  const pathname = usePathname();

  return (
    <header className="surface sticky top-0 z-30 mx-3 mt-3 rounded-2xl px-4 py-3">
      <div className="flex items-center gap-3">
        <button type="button" onClick={onOpenMenu} className="rounded-lg p-2 text-muted-foreground md:hidden">
          <Menu className="h-5 w-5" />
        </button>
        <div className="min-w-0 flex-1">
          <h1 className="truncate font-display text-lg font-semibold">{pageTitle(pathname)}</h1>
          <p className="text-xs text-muted-foreground">Fluxo assistencial em tempo real</p>
        </div>
        <div className="hidden min-w-64 md:block">
          <div className="relative">
            <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <Input className="pl-9" placeholder="Busca rápida..." />
          </div>
        </div>
      </div>
    </header>
  );
}
