"use client";

import { useCallback, useEffect, useState } from "react";
import { toast } from "sonner";
import { api, apiErrorMessage } from "@/lib/api-client";
import { PageHeader } from "@/components/common/page-header";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Select } from "@/components/ui/select";
import { Table, TableContainer, TBody, Td, Th, THead, Tr } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { useLookups } from "@/hooks/use-lookups";
import type { Senha } from "@/types/api";
import { senhaStatusVariant } from "@/lib/status";

const statuses = ["aguardando", "chamado", "em_atendimento", "ausente", "finalizado", "cancelado", "encaminhado"];

export default function SenhasPage() {
  const { data: lookups } = useLookups();
  const [status, setStatus] = useState("");
  const [loading, setLoading] = useState(true);
  const [senhas, setSenhas] = useState<Senha[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const response = await api.get<{ data: Senha[] }>("/senhas", { params: { status } });
      setSenhas(response.data.data);
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setLoading(false);
    }
  }, [status]);

  useEffect(() => {
    load();
  }, [load]);

  async function runAction(senhaId: string, action: string, payload?: Record<string, string>) {
    try {
      await api.post(`/senhas/${senhaId}/${action}`, payload ?? {});
      toast.success("Ação executada.");
      await load();
    } catch (error) {
      toast.error(apiErrorMessage(error));
    }
  }

  return (
    <div className="space-y-4">
      <PageHeader title="Senhas" subtitle="Controle de status, histórico e ações críticas da jornada assistencial." />
      <Card>
        <CardContent className="space-y-4">
          <div className="grid gap-3 md:grid-cols-3">
            <div>
              <p className="mb-1 text-xs text-muted-foreground">Status</p>
              <Select value={status} onChange={(e) => setStatus(e.target.value)}>
                <option value="">Todos</option>
                {statuses.map((item) => (
                  <option key={item} value={item}>
                    {item}
                  </option>
                ))}
              </Select>
            </div>
          </div>

          {loading ? (
            <p className="text-sm text-muted-foreground">Carregando senhas...</p>
          ) : (
            <TableContainer>
              <Table>
                <THead>
                  <Tr>
                    <Th>Senha</Th>
                    <Th>Paciente</Th>
                    <Th>Fila</Th>
                    <Th>Status</Th>
                    <Th>Ações</Th>
                  </Tr>
                </THead>
                <TBody>
                  {senhas.map((senha) => (
                    <Tr key={senha.id}>
                      <Td className="font-semibold">{senha.codigo}</Td>
                      <Td>{senha.paciente?.nome}</Td>
                      <Td>{senha.fila?.nome}</Td>
                      <Td>
                        <Badge variant={senhaStatusVariant(senha.status)}>{senha.status}</Badge>
                      </Td>
                      <Td>
                        <div className="flex flex-wrap gap-2">
                          <Button size="sm" variant="secondary" onClick={() => runAction(senha.id, "rechamar")}>
                            Rechamar
                          </Button>
                          <Button size="sm" variant="outline" onClick={() => runAction(senha.id, "ausente")}>
                            Ausente
                          </Button>
                          <Button size="sm" variant="outline" onClick={() => runAction(senha.id, "cancelar")}>
                            Cancelar
                          </Button>
                          <Button size="sm" variant="ghost" onClick={() => runAction(senha.id, "finalizar")}>
                            Finalizar
                          </Button>
                          <Select
                            className="h-8 w-36"
                            defaultValue=""
                            onChange={(e) => {
                              if (!e.target.value) return;
                              runAction(senha.id, "encaminhar", {
                                fila_id: e.target.value,
                                setor_id: lookups.filas.find((f) => f.id === e.target.value)?.setor_id ?? "",
                              });
                            }}
                          >
                            <option value="">Encaminhar...</option>
                            {lookups.filas.map((fila) => (
                              <option key={fila.id} value={fila.id}>
                                {fila.nome}
                              </option>
                            ))}
                          </Select>
                        </div>
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
