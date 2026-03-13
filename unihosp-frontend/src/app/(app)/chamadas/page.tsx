"use client";

import { useEffect, useState } from "react";
import { toast } from "sonner";
import { api, apiErrorMessage } from "@/lib/api-client";
import { PageHeader } from "@/components/common/page-header";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Table, TableContainer, TBody, Td, Th, THead, Tr } from "@/components/ui/table";
import type { Chamada } from "@/types/api";

export default function ChamadasPage() {
  const [loading, setLoading] = useState(true);
  const [chamadas, setChamadas] = useState<Chamada[]>([]);

  async function load() {
    setLoading(true);
    try {
      const response = await api.get<{ data: Chamada[] }>("/chamadas");
      setChamadas(response.data.data);
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    load();
  }, []);

  async function repeat(chamadaId: string) {
    try {
      await api.post(`/chamadas/${chamadaId}/repeat`);
      toast.success("Chamada repetida.");
      await load();
    } catch (error) {
      toast.error(apiErrorMessage(error));
    }
  }

  return (
    <div className="space-y-4">
      <PageHeader title="Chamadas" subtitle="Histórico de chamadas por setor e repetição de chamadas críticas." />
      <Card>
        <CardContent>
          {loading ? (
            <p className="text-sm text-muted-foreground">Carregando chamadas...</p>
          ) : (
            <TableContainer>
              <Table>
                <THead>
                  <Tr>
                    <Th>Senha</Th>
                    <Th>Paciente</Th>
                    <Th>Tipo</Th>
                    <Th>Horário</Th>
                    <Th />
                  </Tr>
                </THead>
                <TBody>
                  {chamadas.map((chamada) => (
                    <Tr key={chamada.id}>
                      <Td className="font-semibold">{chamada.senha?.codigo}</Td>
                      <Td>{chamada.senha?.paciente?.nome}</Td>
                      <Td>{chamada.tipo}</Td>
                      <Td>{new Date(chamada.chamado_em).toLocaleString("pt-BR")}</Td>
                      <Td>
                        <Button size="sm" variant="outline" onClick={() => repeat(chamada.id)}>
                          Repetir
                        </Button>
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
  );
}
