"use client";

import { useMemo, useState } from "react";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { toast } from "sonner";
import { api, apiErrorMessage } from "@/lib/api-client";
import { PageHeader } from "@/components/common/page-header";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { useLookups } from "@/hooks/use-lookups";
import type { Senha } from "@/types/api";

const schema = z.object({
  pressao_arterial: z.string().optional(),
  temperatura: z.string().optional(),
  saturacao: z.string().optional(),
  frequencia_cardiaca: z.string().optional(),
  peso: z.string().optional(),
  altura: z.string().optional(),
  glicemia: z.string().optional(),
  classificacao_risco: z.enum(["baixo", "medio", "alto", "critico"]),
  observacoes: z.string().optional(),
  encaminhar_fila_id: z.string().uuid().optional().or(z.literal("")),
});

type TriagemForm = z.infer<typeof schema>;

export default function TriagemPage() {
  const { data: lookups } = useLookups();
  const [currentSenha, setCurrentSenha] = useState<Senha | null>(null);
  const [calling, setCalling] = useState(false);
  const [saving, setSaving] = useState(false);

  const triagemFila = useMemo(() => lookups.filas.find((f) => f.tipo === "triagem"), [lookups.filas]);
  const medicaFila = useMemo(() => lookups.filas.find((f) => f.tipo === "medica"), [lookups.filas]);

  const form = useForm<TriagemForm>({
    resolver: zodResolver(schema),
    defaultValues: {
      classificacao_risco: "medio",
      encaminhar_fila_id: "",
    },
  });

  async function chamarProximo() {
    if (!triagemFila) {
      toast.error("Fila de triagem não configurada.");
      return;
    }
    setCalling(true);
    try {
      const response = await api.post<{ data: Senha }>(`/triagem/filas/${triagemFila.id}/chamar-proximo`, {});
      setCurrentSenha(response.data.data);
      form.reset({
        classificacao_risco: "medio",
        encaminhar_fila_id: medicaFila?.id ?? "",
      });
      toast.success(`Paciente ${response.data.data.paciente?.nome} chamado para triagem.`);
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setCalling(false);
    }
  }

  const onSubmit = form.handleSubmit(async (values) => {
    if (!currentSenha) {
      toast.error("Chame um paciente antes de registrar triagem.");
      return;
    }
    setSaving(true);
    try {
      await api.post("/triagem", {
        senha_id: currentSenha.id,
        paciente_id: currentSenha.paciente_id,
        ...values,
      });
      toast.success("Triagem registrada e paciente encaminhado.");
      setCurrentSenha(null);
      form.reset({ classificacao_risco: "medio", encaminhar_fila_id: medicaFila?.id ?? "" });
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setSaving(false);
    }
  });

  return (
    <div className="space-y-4">
      <PageHeader title="Triagem" subtitle="Chame paciente, registre sinais vitais e encaminhe para atendimento médico." />

      <div className="grid gap-4 xl:grid-cols-[1fr,1.3fr]">
        <Card>
          <CardHeader>
            <CardTitle>Paciente atual</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <Button fullWidth onClick={chamarProximo} disabled={calling} type="button">
              {calling ? "Chamando..." : "Chamar próximo da triagem"}
            </Button>
            {currentSenha ? (
              <div className="rounded-2xl border border-border bg-muted/50 p-4">
                <p className="font-display text-3xl font-semibold text-primary">{currentSenha.codigo}</p>
                <p className="mt-1 font-semibold">{currentSenha.paciente?.nome}</p>
                <div className="mt-2 flex gap-2">
                  <Badge variant="warning">{currentSenha.prioridade}</Badge>
                  <Badge>{currentSenha.tipo_atendimento}</Badge>
                </div>
              </div>
            ) : (
              <p className="text-sm text-muted-foreground">Nenhum paciente em triagem no momento.</p>
            )}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Registro de sinais vitais</CardTitle>
          </CardHeader>
          <CardContent>
            <form className="grid gap-3 sm:grid-cols-2" onSubmit={onSubmit}>
              <div>
                <Label>Pressão arterial</Label>
                <Input placeholder="120x80" {...form.register("pressao_arterial")} />
              </div>
              <div>
                <Label>Temperatura</Label>
                <Input type="number" step="0.1" {...form.register("temperatura")} />
              </div>
              <div>
                <Label>Saturação</Label>
                <Input type="number" {...form.register("saturacao")} />
              </div>
              <div>
                <Label>Frequência cardíaca</Label>
                <Input type="number" {...form.register("frequencia_cardiaca")} />
              </div>
              <div>
                <Label>Peso (kg)</Label>
                <Input type="number" step="0.1" {...form.register("peso")} />
              </div>
              <div>
                <Label>Altura (m)</Label>
                <Input type="number" step="0.01" {...form.register("altura")} />
              </div>
              <div>
                <Label>Glicemia</Label>
                <Input type="number" step="0.1" {...form.register("glicemia")} />
              </div>
              <div>
                <Label>Classificação de risco</Label>
                <Select {...form.register("classificacao_risco")}>
                  <option value="baixo">Baixo</option>
                  <option value="medio">Médio</option>
                  <option value="alto">Alto</option>
                  <option value="critico">Crítico</option>
                </Select>
              </div>
              <div className="sm:col-span-2">
                <Label>Encaminhar para fila</Label>
                <Select {...form.register("encaminhar_fila_id")}>
                  <option value="">Não encaminhar</option>
                  {lookups.filas.map((fila) => (
                    <option key={fila.id} value={fila.id}>
                      {fila.nome}
                    </option>
                  ))}
                </Select>
              </div>
              <div className="sm:col-span-2">
                <Label>Observações</Label>
                <Textarea rows={3} {...form.register("observacoes")} />
              </div>
              <div className="sm:col-span-2">
                <Button disabled={saving || !currentSenha} fullWidth type="submit">
                  {saving ? "Salvando..." : "Registrar triagem"}
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
