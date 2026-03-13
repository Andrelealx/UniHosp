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
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { useLookups } from "@/hooks/use-lookups";
import type { Senha } from "@/types/api";

const schema = z.object({
  queixa_principal: z.string().min(3, "Queixa principal obrigatória"),
  hipotese_diagnostica: z.string().optional(),
  cid_codigo: z.string().optional(),
  conduta: z.string().optional(),
  prescricao_texto: z.string().optional(),
  orientacoes: z.string().optional(),
});

type AtendimentoForm = z.infer<typeof schema>;

export default function AtendimentosPage() {
  const { data: lookups } = useLookups();
  const [currentSenha, setCurrentSenha] = useState<Senha | null>(null);
  const [calling, setCalling] = useState(false);
  const [saving, setSaving] = useState(false);
  const medicaFila = useMemo(() => lookups.filas.find((f) => f.tipo === "medica"), [lookups.filas]);

  const form = useForm<AtendimentoForm>({
    resolver: zodResolver(schema),
    defaultValues: {
      queixa_principal: "",
    },
  });

  async function chamarProximo() {
    if (!medicaFila) {
      toast.error("Fila médica não configurada.");
      return;
    }
    setCalling(true);
    try {
      const response = await api.post<{ data: Senha }>(`/atendimentos/filas/${medicaFila.id}/chamar-proximo`, {});
      setCurrentSenha(response.data.data);
      toast.success(`Paciente ${response.data.data.paciente?.nome} chamado para atendimento médico.`);
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setCalling(false);
    }
  }

  const onSubmit = form.handleSubmit(async (values) => {
    if (!currentSenha) {
      toast.error("Nenhum paciente selecionado.");
      return;
    }
    setSaving(true);
    try {
      await api.post("/atendimentos", {
        senha_id: currentSenha.id,
        paciente_id: currentSenha.paciente_id,
        ...values,
      });
      toast.success("Atendimento registrado com sucesso.");
      setCurrentSenha(null);
      form.reset({ queixa_principal: "" });
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setSaving(false);
    }
  });

  return (
    <div className="space-y-4">
      <PageHeader title="Atendimento médico" subtitle="Chame o próximo paciente e registre evolução, CID, conduta e prescrição." />
      <div className="grid gap-4 xl:grid-cols-[1fr,1.3fr]">
        <Card>
          <CardHeader>
            <CardTitle>Paciente em consulta</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <Button fullWidth onClick={chamarProximo} disabled={calling} type="button">
              {calling ? "Chamando..." : "Chamar próximo da fila médica"}
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
              <p className="text-sm text-muted-foreground">Sem paciente em atendimento no momento.</p>
            )}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Registro clínico básico</CardTitle>
          </CardHeader>
          <CardContent>
            <form className="space-y-3" onSubmit={onSubmit}>
              <div>
                <Label>Queixa principal</Label>
                <Textarea rows={2} {...form.register("queixa_principal")} />
              </div>
              <div>
                <Label>Hipótese diagnóstica</Label>
                <Textarea rows={2} {...form.register("hipotese_diagnostica")} />
              </div>
              <div className="grid gap-3 sm:grid-cols-2">
                <div>
                  <Label>CID</Label>
                  <Input {...form.register("cid_codigo")} placeholder="Ex: J06.9" />
                </div>
                <div>
                  <Label>Conduta</Label>
                  <Input {...form.register("conduta")} placeholder="Ex: Observação + analgesia" />
                </div>
              </div>
              <div>
                <Label>Prescrição simples</Label>
                <Textarea rows={3} {...form.register("prescricao_texto")} />
              </div>
              <div>
                <Label>Orientações</Label>
                <Textarea rows={2} {...form.register("orientacoes")} />
              </div>
              <Button disabled={saving || !currentSenha} fullWidth type="submit">
                {saving ? "Salvando..." : "Finalizar atendimento"}
              </Button>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
