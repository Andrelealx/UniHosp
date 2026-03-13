"use client";

import { useEffect, useMemo, useState } from "react";
import { toast } from "sonner";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { PageHeader } from "@/components/common/page-header";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { api, apiErrorMessage } from "@/lib/api-client";
import { useLookups } from "@/hooks/use-lookups";
import type { Paciente, Senha } from "@/types/api";

const schema = z.object({
  tipo_atendimento: z.enum(["consulta", "retorno", "exame", "urgencia", "triagem"]),
  prioridade: z.enum(["normal", "prioritario", "urgente"]),
  fila_id: z.string().uuid("Selecione uma fila"),
  setor_id: z.string().uuid("Setor obrigatório"),
  observacoes_iniciais: z.string().optional(),
});

type FormData = z.infer<typeof schema>;

export default function RecepcaoPage() {
  const { data: lookups } = useLookups();
  const [q, setQ] = useState("");
  const [pacientes, setPacientes] = useState<Paciente[]>([]);
  const [selectedPaciente, setSelectedPaciente] = useState<Paciente | null>(null);
  const [lastSenha, setLastSenha] = useState<Senha | null>(null);
  const [saving, setSaving] = useState(false);
  const [creating, setCreating] = useState(false);

  const form = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: {
      tipo_atendimento: "consulta",
      prioridade: "normal",
    },
  });

  useEffect(() => {
    if (!q.trim()) return;
    const timeout = setTimeout(async () => {
      try {
        const response = await api.get<{ data: Paciente[] }>("/recepcao/buscar-paciente", { params: { q } });
        setPacientes(response.data.data);
      } catch (error) {
        toast.error(apiErrorMessage(error));
      }
    }, 250);

    return () => clearTimeout(timeout);
  }, [q]);

  const selectedFila = useMemo(
    () => lookups.filas.find((fila) => fila.id === form.watch("fila_id")),
    [form, lookups.filas],
  );

  useEffect(() => {
    if (!selectedFila) return;
    form.setValue("setor_id", selectedFila.setor_id);
  }, [form, selectedFila]);

  const onEmitirSenha = form.handleSubmit(async (values) => {
    if (!selectedPaciente) {
      toast.error("Selecione um paciente.");
      return;
    }

    setSaving(true);
    try {
      const response = await api.post<{ data: Senha }>("/recepcao/emitir-senha", {
        paciente_id: selectedPaciente.id,
        ...values,
      });
      setLastSenha(response.data.data);
      toast.success(`Senha ${response.data.data.codigo} emitida.`);
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setSaving(false);
    }
  });

  async function quickCreatePaciente() {
    setCreating(true);
    try {
      const response = await api.post<{ data: Paciente }>("/recepcao/cadastro-rapido", {
        nome: q || "Paciente sem nome",
      });
      setSelectedPaciente(response.data.data);
      toast.success("Paciente criado na recepção.");
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setCreating(false);
    }
  }

  return (
    <div className="space-y-4">
      <PageHeader title="Recepção" subtitle="Localize/cadastre paciente, abra ficha e emita senha com prioridade." />
      <div className="grid gap-4 xl:grid-cols-[1.4fr,1fr]">
        <Card>
          <CardHeader>
            <CardTitle>1. Buscar ou cadastrar paciente</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <Input placeholder="Nome, CPF, CNS ou telefone..." value={q} onChange={(e) => setQ(e.target.value)} />
            <div className="space-y-2">
              {pacientes.map((paciente) => (
                <button
                  key={paciente.id}
                  type="button"
                  onClick={() => setSelectedPaciente(paciente)}
                  className="w-full rounded-xl border border-border bg-card p-3 text-left transition hover:bg-muted"
                >
                  <p className="font-semibold">{paciente.nome}</p>
                  <p className="text-xs text-muted-foreground">{paciente.cpf ?? "Sem CPF"} · {paciente.telefone ?? "Sem telefone"}</p>
                </button>
              ))}
            </div>
            <Button disabled={creating} variant="outline" onClick={quickCreatePaciente} type="button">
              {creating ? "Criando..." : "Cadastro rápido com nome buscado"}
            </Button>
            {selectedPaciente ? (
              <div className="rounded-xl border border-primary/40 bg-primary/5 p-3">
                <p className="text-xs text-muted-foreground">Paciente selecionado</p>
                <p className="font-semibold">{selectedPaciente.nome}</p>
              </div>
            ) : null}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>2. Emitir senha</CardTitle>
          </CardHeader>
          <CardContent>
            <form className="space-y-3" onSubmit={onEmitirSenha}>
              <div>
                <Label>Tipo de atendimento</Label>
                <Select {...form.register("tipo_atendimento")}>
                  {lookups.tipos_atendimento.map((item) => (
                    <option key={item} value={item}>
                      {item}
                    </option>
                  ))}
                </Select>
              </div>
              <div>
                <Label>Prioridade</Label>
                <Select {...form.register("prioridade")}>
                  {lookups.prioridades.map((item) => (
                    <option key={item} value={item}>
                      {item}
                    </option>
                  ))}
                </Select>
              </div>
              <div>
                <Label>Fila</Label>
                <Select {...form.register("fila_id")}>
                  <option value="">Selecione</option>
                  {lookups.filas.map((fila) => (
                    <option key={fila.id} value={fila.id}>
                      {fila.nome}
                    </option>
                  ))}
                </Select>
              </div>
              <div>
                <Label>Setor</Label>
                <Input value={lookups.setores.find((s) => s.id === form.watch("setor_id"))?.nome ?? ""} readOnly />
              </div>
              <div>
                <Label>Observações iniciais</Label>
                <Textarea rows={3} {...form.register("observacoes_iniciais")} />
              </div>
              <Button disabled={saving} fullWidth type="submit">
                {saving ? "Emitindo..." : "Emitir senha"}
              </Button>
            </form>

            {lastSenha ? (
              <div className="mt-4 rounded-xl border border-border bg-muted/40 p-3">
                <p className="text-xs text-muted-foreground">Comprovante simples</p>
                <p className="font-display text-2xl font-semibold text-primary">{lastSenha.codigo}</p>
                <div className="mt-2 flex flex-wrap gap-2">
                  <Badge variant="info">{lastSenha.tipo_atendimento}</Badge>
                  <Badge variant="warning">{lastSenha.prioridade}</Badge>
                  <Badge>{lastSenha.status}</Badge>
                </div>
              </div>
            ) : null}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
