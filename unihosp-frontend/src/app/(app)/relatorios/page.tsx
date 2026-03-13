"use client";

import { useState } from "react";
import { toast } from "sonner";
import { api, apiErrorMessage } from "@/lib/api-client";
import { useLookups } from "@/hooks/use-lookups";
import { PageHeader } from "@/components/common/page-header";
import { StatCard } from "@/components/common/stat-card";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Select } from "@/components/ui/select";

interface RelatorioData {
  pacientes_cadastrados: number;
  pacientes_atendidos: number;
  senhas_emitidas: number;
  tempo_medio_espera_minutos: number;
  triagens_realizadas: number;
  atendimentos_medicos: number;
  chamadas_realizadas: number;
}

export default function RelatoriosPage() {
  const { data: lookups } = useLookups();
  const [inicio, setInicio] = useState(new Date().toISOString().slice(0, 10));
  const [fim, setFim] = useState(new Date().toISOString().slice(0, 10));
  const [setorId, setSetorId] = useState("");
  const [filaId, setFilaId] = useState("");
  const [loading, setLoading] = useState(false);
  const [data, setData] = useState<RelatorioData | null>(null);

  async function load() {
    setLoading(true);
    try {
      const response = await api.get<RelatorioData>("/relatorios", {
        params: {
          inicio,
          fim,
          setor_id: setorId || undefined,
          fila_id: filaId || undefined,
        },
      });
      setData(response.data);
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="space-y-4">
      <PageHeader title="Relatórios iniciais" subtitle="Indicadores por período para validação do MVP e eficiência operacional." />

      <Card>
        <CardContent className="grid gap-3 md:grid-cols-5">
          <Input type="date" value={inicio} onChange={(e) => setInicio(e.target.value)} />
          <Input type="date" value={fim} onChange={(e) => setFim(e.target.value)} />
          <Select value={setorId} onChange={(e) => setSetorId(e.target.value)}>
            <option value="">Todos os setores</option>
            {lookups.setores.map((setor) => (
              <option key={setor.id} value={setor.id}>
                {setor.nome}
              </option>
            ))}
          </Select>
          <Select value={filaId} onChange={(e) => setFilaId(e.target.value)}>
            <option value="">Todas as filas</option>
            {lookups.filas.map((fila) => (
              <option key={fila.id} value={fila.id}>
                {fila.nome}
              </option>
            ))}
          </Select>
          <Button onClick={load} disabled={loading}>
            {loading ? "Gerando..." : "Gerar relatório"}
          </Button>
        </CardContent>
      </Card>

      {data ? (
        <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
          <StatCard label="Pacientes cadastrados" value={data.pacientes_cadastrados} />
          <StatCard label="Pacientes atendidos" value={data.pacientes_atendidos} />
          <StatCard label="Senhas emitidas" value={data.senhas_emitidas} />
          <StatCard label="Tempo médio de espera" value={`${data.tempo_medio_espera_minutos} min`} />
          <StatCard label="Triagens realizadas" value={data.triagens_realizadas} />
          <StatCard label="Atendimentos médicos" value={data.atendimentos_medicos} />
          <StatCard label="Chamadas realizadas" value={data.chamadas_realizadas} />
        </div>
      ) : null}
    </div>
  );
}
