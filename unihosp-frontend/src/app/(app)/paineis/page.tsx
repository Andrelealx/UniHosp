"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { toast } from "sonner";
import { api, apiErrorMessage } from "@/lib/api-client";
import { useLookups } from "@/hooks/use-lookups";
import { PageHeader } from "@/components/common/page-header";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import type { Painel } from "@/types/api";

const emptyForm = {
  nome: "",
  slug: "",
  tipo: "recepcao",
  setor_id: "",
  mensagem_institucional: "",
  forma_exibicao_paciente: "senha",
};

export default function PaineisPage() {
  const { data: lookups } = useLookups();
  const [paineis, setPaineis] = useState<Painel[]>([]);
  const [form, setForm] = useState(emptyForm);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [saving, setSaving] = useState(false);

  async function load() {
    const response = await api.get<{ data: Painel[] }>("/paineis");
    setPaineis(response.data.data);
  }

  useEffect(() => {
    load().catch((error) => toast.error(apiErrorMessage(error)));
  }, []);

  async function save() {
    setSaving(true);
    try {
      if (editingId) {
        await api.put(`/paineis/${editingId}`, form);
        toast.success("Painel atualizado.");
      } else {
        await api.post("/paineis", form);
        toast.success("Painel criado.");
      }
      setForm(emptyForm);
      setEditingId(null);
      await load();
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setSaving(false);
    }
  }

  return (
    <div className="space-y-4">
      <PageHeader title="Painéis TV" subtitle="Configuração administrativa de painéis por setor e modo de exibição LGPD." />

      <div className="grid gap-4 xl:grid-cols-[1.2fr,1fr]">
        <Card>
          <CardHeader>
            <CardTitle>Painéis cadastrados</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            {paineis.map((painel) => (
              <div key={painel.id} className="rounded-xl border border-border bg-card p-3">
                <div className="flex flex-wrap items-center justify-between gap-2">
                  <div>
                    <p className="font-semibold">{painel.nome}</p>
                    <p className="text-xs text-muted-foreground">/{painel.slug}</p>
                  </div>
                  <div className="flex items-center gap-2">
                    <Badge variant={painel.ativo ? "success" : "danger"}>{painel.ativo ? "ativo" : "inativo"}</Badge>
                    <Button
                      size="sm"
                      variant="outline"
                      onClick={() => {
                        setEditingId(painel.id);
                        setForm({
                          nome: painel.nome,
                          slug: painel.slug,
                          tipo: painel.tipo,
                          setor_id: painel.setor_id ?? "",
                          mensagem_institucional: painel.mensagem_institucional ?? "",
                          forma_exibicao_paciente: painel.forma_exibicao_paciente,
                        });
                      }}
                    >
                      Editar
                    </Button>
                    <Button
                      size="sm"
                      variant="ghost"
                      onClick={async () => {
                        await api.post(`/paineis/${painel.id}/toggle`);
                        await load();
                      }}
                    >
                      Toggle
                    </Button>
                    <Link href={`/painel/${painel.slug}`} target="_blank" className="text-xs text-primary underline">
                      Abrir TV
                    </Link>
                  </div>
                </div>
              </div>
            ))}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>{editingId ? "Editar painel" : "Novo painel"}</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div>
              <Label>Nome do painel</Label>
              <Input value={form.nome} onChange={(e) => setForm((old) => ({ ...old, nome: e.target.value }))} />
            </div>
            <div>
              <Label>Slug</Label>
              <Input value={form.slug} onChange={(e) => setForm((old) => ({ ...old, slug: e.target.value }))} />
            </div>
            <div className="grid gap-3 sm:grid-cols-2">
              <div>
                <Label>Tipo</Label>
                <Select value={form.tipo} onChange={(e) => setForm((old) => ({ ...old, tipo: e.target.value }))}>
                  <option value="recepcao">Recepção</option>
                  <option value="triagem">Triagem</option>
                  <option value="medico">Médico</option>
                </Select>
              </div>
              <div>
                <Label>Setor</Label>
                <Select value={form.setor_id} onChange={(e) => setForm((old) => ({ ...old, setor_id: e.target.value }))}>
                  <option value="">Sem vínculo</option>
                  {lookups.setores.map((setor) => (
                    <option key={setor.id} value={setor.id}>
                      {setor.nome}
                    </option>
                  ))}
                </Select>
              </div>
            </div>
            <div>
              <Label>Exibição do paciente</Label>
              <Select
                value={form.forma_exibicao_paciente}
                onChange={(e) => setForm((old) => ({ ...old, forma_exibicao_paciente: e.target.value }))}
              >
                <option value="senha">Somente senha</option>
                <option value="senha_iniciais">Senha + iniciais</option>
                <option value="senha_primeiro_nome">Senha + primeiro nome e inicial</option>
              </Select>
            </div>
            <div>
              <Label>Mensagem institucional</Label>
              <Textarea
                rows={3}
                value={form.mensagem_institucional}
                onChange={(e) => setForm((old) => ({ ...old, mensagem_institucional: e.target.value }))}
              />
            </div>
            <div className="grid grid-cols-2 gap-2">
              <Button onClick={save} disabled={saving}>
                {saving ? "Salvando..." : editingId ? "Atualizar" : "Criar painel"}
              </Button>
              <Button
                variant="outline"
                onClick={() => {
                  setEditingId(null);
                  setForm(emptyForm);
                }}
              >
                Limpar
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
