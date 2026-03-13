"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { ChevronRight } from "lucide-react";
import { api } from "@/lib/api-client";
import { PageHeader } from "@/components/common/page-header";
import { StatCard } from "@/components/common/stat-card";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Table, TableContainer, TBody, Td, Th, THead, Tr } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { senhaStatusVariant } from "@/lib/status";

interface DashboardData {
  indicadores: {
    pacientes_atendidos_hoje: number;
    pacientes_aguardando: number;
    senhas_aguardando: number;
    triagens_realizadas_hoje: number;
    atendimentos_medicos_hoje: number;
  };
  chamadas_recentes: Array<{
    id: string;
    senha: string;
    paciente: string;
    setor: string;
    sala: string;
    status: string;
    chamado_em: string;
  }>;
}

export default function DashboardPage() {
  const [data, setData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let mounted = true;

    async function load() {
      try {
        const response = await api.get<DashboardData>("/dashboard");
        if (mounted) {
          setData(response.data);
        }
      } finally {
        if (mounted) {
          setLoading(false);
        }
      }
    }

    load();

    return () => {
      mounted = false;
    };
  }, []);

  return (
    <div className="space-y-4">
      <PageHeader
        title="Visão geral da operação"
        subtitle="Indicadores em tempo real para recepção, triagem e atendimento."
      />

      <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <StatCard label="Atendidos Hoje" value={data?.indicadores.pacientes_atendidos_hoje ?? 0} />
        <StatCard label="Aguardando" value={data?.indicadores.pacientes_aguardando ?? 0} />
        <StatCard label="Senhas na espera" value={data?.indicadores.senhas_aguardando ?? 0} />
        <StatCard label="Triagens Hoje" value={data?.indicadores.triagens_realizadas_hoje ?? 0} />
        <StatCard label="Consultas Hoje" value={data?.indicadores.atendimentos_medicos_hoje ?? 0} />
      </div>

      <div className="grid gap-4 xl:grid-cols-[2fr,1fr]">
        <Card>
          <CardHeader>
            <CardTitle>Chamadas recentes</CardTitle>
          </CardHeader>
          <CardContent>
            {loading ? (
              <p className="text-sm text-muted-foreground">Carregando...</p>
            ) : (
              <TableContainer>
                <Table>
                  <THead>
                    <Tr>
                      <Th>Senha</Th>
                      <Th>Paciente</Th>
                      <Th>Setor/Sala</Th>
                      <Th>Status</Th>
                    </Tr>
                  </THead>
                  <TBody>
                    {(data?.chamadas_recentes ?? []).map((call) => (
                      <Tr key={call.id}>
                        <Td className="font-semibold">{call.senha}</Td>
                        <Td>{call.paciente}</Td>
                        <Td>
                          {call.setor} {call.sala ? `· ${call.sala}` : ""}
                        </Td>
                        <Td>
                          <Badge variant={senhaStatusVariant(call.status)}>{call.status}</Badge>
                        </Td>
                      </Tr>
                    ))}
                  </TBody>
                </Table>
              </TableContainer>
            )}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Atalhos rápidos</CardTitle>
          </CardHeader>
          <CardContent className="space-y-2">
            {[
              { href: "/recepcao", label: "Emitir senha" },
              { href: "/triagem", label: "Nova triagem" },
              { href: "/atendimentos", label: "Registrar atendimento" },
              { href: "/paineis", label: "Configurar painel TV" },
            ].map((shortcut) => (
              <Link
                key={shortcut.href}
                href={shortcut.href}
                className="flex items-center justify-between rounded-xl border border-border bg-card px-3 py-2 text-sm hover:bg-muted"
              >
                {shortcut.label}
                <ChevronRight className="h-4 w-4 text-muted-foreground" />
              </Link>
            ))}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
