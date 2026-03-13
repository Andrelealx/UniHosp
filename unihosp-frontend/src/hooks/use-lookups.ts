"use client";

import { useEffect, useState } from "react";
import { api } from "@/lib/api-client";
import type { Convenio, Fila, Painel, Sala, Setor } from "@/types/api";

interface LookupResponse {
  convenios: Convenio[];
  setores: Setor[];
  salas: Sala[];
  filas: Fila[];
  paineis: Painel[];
  roles: string[];
  tipos_atendimento: string[];
  prioridades: string[];
  status_senha: string[];
}

const initial: LookupResponse = {
  convenios: [],
  setores: [],
  salas: [],
  filas: [],
  paineis: [],
  roles: [],
  tipos_atendimento: [],
  prioridades: [],
  status_senha: [],
};

export function useLookups() {
  const [data, setData] = useState<LookupResponse>(initial);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let mounted = true;

    async function load() {
      try {
        const response = await api.get<LookupResponse>("/lookups");
        if (mounted) setData(response.data);
      } finally {
        if (mounted) setLoading(false);
      }
    }

    load();
    return () => {
      mounted = false;
    };
  }, []);

  return { data, loading };
}
