"use client";

import { useState } from "react";
import { toast } from "sonner";
import { api, apiErrorMessage } from "@/lib/api-client";
import { PageHeader } from "@/components/common/page-header";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import type { Paciente } from "@/types/api";

interface ProntuarioResponse {
  data: {
    resumo_clinico?: string | null;
    alergias?: string | null;
    comorbidades?: string | null;
    observacoes?: string | null;
    paciente?: Paciente;
  };
  timeline: Array<{
    tipo: string;
    data: string;
    titulo: string;
    detalhes?: string | null;
  }>;
}

export default function ProntuariosPage() {
  const [q, setQ] = useState("");
  const [pacientes, setPacientes] = useState<Paciente[]>([]);
  const [prontuario, setProntuario] = useState<ProntuarioResponse | null>(null);

  async function search() {
    if (!q.trim()) return;
    try {
      const response = await api.get<{ data: Paciente[] }>("/pacientes", { params: { q } });
      setPacientes(response.data.data);
    } catch (error) {
      toast.error(apiErrorMessage(error));
    }
  }

  async function loadProntuario(pacienteId: string) {
    try {
      const response = await api.get<ProntuarioResponse>(`/prontuarios/paciente/${pacienteId}`);
      setProntuario(response.data);
    } catch (error) {
      toast.error(apiErrorMessage(error));
    }
  }

  return (
    <div className="space-y-4">
      <PageHeader title="Prontuário básico" subtitle="Linha do tempo clínica com histórico de triagens e atendimentos." />

      <Card>
        <CardContent className="space-y-3">
          <Input
            placeholder="Busque paciente por nome, CPF ou CNS..."
            value={q}
            onChange={(e) => setQ(e.target.value)}
            onKeyDown={(e) => (e.key === "Enter" ? search() : null)}
          />
          <div className="grid gap-2 md:grid-cols-2">
            {pacientes.map((paciente) => (
              <button
                key={paciente.id}
                onClick={() => loadProntuario(paciente.id)}
                className="rounded-xl border border-border bg-card p-3 text-left hover:bg-muted"
                type="button"
              >
                <p className="font-semibold">{paciente.nome}</p>
                <p className="text-xs text-muted-foreground">{paciente.cpf ?? "Sem CPF"}</p>
              </button>
            ))}
          </div>
        </CardContent>
      </Card>

      {prontuario ? (
        <div className="grid gap-4 xl:grid-cols-[1fr,1.5fr]">
          <Card>
            <CardHeader>
              <CardTitle>Resumo clínico</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3 text-sm">
              <div>
                <p className="text-xs text-muted-foreground">Paciente</p>
                <p className="font-semibold">{prontuario.data.paciente?.nome}</p>
              </div>
              <div>
                <p className="text-xs text-muted-foreground">Alergias</p>
                <p>{prontuario.data.alergias || "Não informado"}</p>
              </div>
              <div>
                <p className="text-xs text-muted-foreground">Comorbidades</p>
                <p>{prontuario.data.comorbidades || "Não informado"}</p>
              </div>
              <div>
                <p className="text-xs text-muted-foreground">Observações</p>
                <p>{prontuario.data.observacoes || "Sem observações."}</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Linha do tempo</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {prontuario.timeline.map((event, index) => (
                  <div key={`${event.tipo}-${index}`} className="rounded-xl border border-border bg-card p-3">
                    <p className="text-xs uppercase tracking-wide text-muted-foreground">{event.tipo}</p>
                    <p className="font-semibold">{event.titulo}</p>
                    <p className="text-xs text-muted-foreground">{new Date(event.data).toLocaleString("pt-BR")}</p>
                    {event.detalhes ? <p className="mt-1 text-sm">{event.detalhes}</p> : null}
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      ) : null}
    </div>
  );
}
