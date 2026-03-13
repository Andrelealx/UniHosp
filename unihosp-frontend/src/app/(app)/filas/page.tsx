"use client";

import { useEffect, useState } from "react";
import { toast } from "sonner";
import { BellRing } from "lucide-react";
import { api, apiErrorMessage } from "@/lib/api-client";
import { getEcho } from "@/lib/realtime";
import { PageHeader } from "@/components/common/page-header";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Table, TableContainer, TBody, Td, Th, THead, Tr } from "@/components/ui/table";
import type { Senha } from "@/types/api";

interface Snapshot {
  filas: Array<{
    id: string;
    nome: string;
    tipo: string;
    setor: string;
    aguardando: number;
    chamado: number;
    em_atendimento: number;
    ausente: number;
  }>;
}

export default function FilasPage() {
  const [snapshot, setSnapshot] = useState<Snapshot>({ filas: [] });
  const [selectedFila, setSelectedFila] = useState<string | null>(null);
  const [senhas, setSenhas] = useState<Senha[]>([]);
  const [loading, setLoading] = useState(false);

  async function loadSnapshot() {
    const response = await api.get<Snapshot>("/filas");
    setSnapshot(response.data);
  }

  async function loadFilaDetails(filaId: string) {
    setLoading(true);
    try {
      const response = await api.get<{ senhas: Senha[] }>(`/filas/${filaId}`);
      setSenhas(response.data.senhas);
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadSnapshot().catch(() => undefined);
    const interval = setInterval(() => {
      loadSnapshot().catch(() => undefined);
      if (selectedFila) loadFilaDetails(selectedFila).catch(() => undefined);
    }, 5000);

    const echo = getEcho();
    if (echo) {
      echo.channel("unihosp.senhas").listen(".PainelAtualizado", () => {
        loadSnapshot().catch(() => undefined);
        if (selectedFila) loadFilaDetails(selectedFila).catch(() => undefined);
      });
    }

    return () => {
      clearInterval(interval);
      if (echo) echo.leave("unihosp.senhas");
    };
  }, [selectedFila]);

  return (
    <div className="space-y-4">
      <PageHeader title="Filas e operação" subtitle="Acompanhe filas ativas e chame o próximo paciente com prioridade." />

      <div className="grid gap-4 xl:grid-cols-[1.2fr,1fr]">
        <Card>
          <CardHeader>
            <CardTitle>Filas ativas</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid gap-3 sm:grid-cols-2">
              {snapshot.filas.map((fila) => (
                <button
                  key={fila.id}
                  type="button"
                  onClick={() => {
                    setSelectedFila(fila.id);
                    loadFilaDetails(fila.id).catch(() => undefined);
                  }}
                  className="rounded-2xl border border-border bg-card p-4 text-left hover:bg-muted"
                >
                  <div className="mb-2 flex items-center justify-between">
                    <p className="font-semibold">{fila.nome}</p>
                    <Badge>{fila.tipo}</Badge>
                  </div>
                  <p className="text-xs text-muted-foreground">{fila.setor}</p>
                  <div className="mt-3 flex flex-wrap gap-2 text-xs">
                    <Badge variant="warning">Aguardando {fila.aguardando}</Badge>
                    <Badge variant="info">Chamado {fila.chamado}</Badge>
                    <Badge>Atendimento {fila.em_atendimento}</Badge>
                  </div>
                </button>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex-row items-center justify-between">
            <CardTitle>Senhas na fila</CardTitle>
            {selectedFila ? (
              <Button
                size="sm"
                onClick={async () => {
                  try {
                    await api.post(`/filas/${selectedFila}/chamar-proximo`, {});
                    toast.success("Próximo paciente chamado.");
                    await loadSnapshot();
                    await loadFilaDetails(selectedFila);
                  } catch (error) {
                    toast.error(apiErrorMessage(error));
                  }
                }}
              >
                <BellRing className="h-4 w-4" />
                Chamar próximo
              </Button>
            ) : null}
          </CardHeader>
          <CardContent>
            {loading ? (
              <p className="text-sm text-muted-foreground">Atualizando fila...</p>
            ) : (
              <TableContainer>
                <Table>
                  <THead>
                    <Tr>
                      <Th>Senha</Th>
                      <Th>Paciente</Th>
                      <Th>Status</Th>
                    </Tr>
                  </THead>
                  <TBody>
                    {senhas.map((senha) => (
                      <Tr key={senha.id}>
                        <Td className="font-semibold">{senha.codigo}</Td>
                        <Td>{senha.paciente?.nome}</Td>
                        <Td>
                          <Badge>{senha.status}</Badge>
                        </Td>
                      </Tr>
                    ))}
                  </TBody>
                </Table>
              </TableContainer>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
