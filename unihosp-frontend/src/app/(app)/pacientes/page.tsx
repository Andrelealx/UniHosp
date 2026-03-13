"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
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
import { Table, TableContainer, TBody, Td, Th, THead, Tr } from "@/components/ui/table";
import { useLookups } from "@/hooks/use-lookups";
import type { Paciente } from "@/types/api";
import { maskCpf } from "@/lib/utils";

const schema = z.object({
  nome: z.string().min(3, "Nome obrigatório"),
  data_nascimento: z.string().optional().or(z.literal("")),
  sexo: z.string().optional(),
  nome_mae: z.string().optional(),
  cpf: z.string().optional(),
  cns: z.string().optional(),
  rg: z.string().optional(),
  telefone: z.string().optional(),
  convenio_id: z.string().optional(),
  alergias: z.string().optional(),
  comorbidades: z.string().optional(),
  observacoes: z.string().optional(),
});

type FormData = z.infer<typeof schema>;

export default function PacientesPage() {
  const { data: lookups } = useLookups();
  const [q, setQ] = useState("");
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [pacientes, setPacientes] = useState<Paciente[]>([]);
  const [editing, setEditing] = useState<Paciente | null>(null);

  const form = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: {
      nome: "",
      sexo: "O",
    },
  });

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const response = await api.get<{ data: Paciente[] }>("/pacientes", { params: { q } });
      setPacientes(response.data.data);
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setLoading(false);
    }
  }, [q]);

  useEffect(() => {
    load();
  }, [load]);

  useEffect(() => {
    if (!editing) return;
    form.reset({
      nome: editing.nome,
      data_nascimento: editing.data_nascimento ?? "",
      sexo: editing.sexo ?? "O",
      nome_mae: editing.nome_mae ?? "",
      cpf: editing.cpf ?? "",
      cns: editing.cns ?? "",
      rg: editing.rg ?? "",
      telefone: editing.telefone ?? "",
      convenio_id: editing.convenio_id ?? "",
      alergias: editing.alergias ?? "",
      comorbidades: editing.comorbidades ?? "",
      observacoes: editing.observacoes ?? "",
    });
  }, [editing, form]);

  const onSubmit = form.handleSubmit(async (values) => {
    setSaving(true);
    try {
      if (editing) {
        await api.put(`/pacientes/${editing.id}`, values);
        toast.success("Paciente atualizado.");
      } else {
        await api.post("/pacientes", values);
        toast.success("Paciente cadastrado.");
      }
      setEditing(null);
      form.reset({ nome: "", sexo: "O" });
      await load();
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setSaving(false);
    }
  });

  const title = useMemo(() => (editing ? "Editar paciente" : "Novo paciente"), [editing]);

  return (
    <div className="space-y-4">
      <PageHeader title="Pacientes" subtitle="Cadastro clínico com busca rápida por nome, CPF, CNS e telefone." />

      <div className="grid gap-4 xl:grid-cols-[2fr,1fr]">
        <Card>
          <CardHeader className="space-y-3">
            <CardTitle>Lista de pacientes</CardTitle>
            <Input placeholder="Buscar por nome, CPF, CNS ou telefone..." value={q} onChange={(e) => setQ(e.target.value)} />
          </CardHeader>
          <CardContent>
            {loading ? (
              <p className="text-sm text-muted-foreground">Carregando pacientes...</p>
            ) : (
              <>
                <div className="space-y-2 md:hidden">
                  {pacientes.map((paciente) => (
                    <button
                      key={paciente.id}
                      type="button"
                      className="w-full rounded-xl border border-border bg-card p-3 text-left"
                      onClick={() => setEditing(paciente)}
                    >
                      <p className="font-semibold">{paciente.nome}</p>
                      <p className="text-xs text-muted-foreground">
                        {maskCpf(paciente.cpf)} · {paciente.telefone ?? "Sem telefone"}
                      </p>
                    </button>
                  ))}
                </div>

                <div className="hidden md:block">
                  <TableContainer>
                    <Table>
                      <THead>
                        <Tr>
                          <Th>Nome</Th>
                          <Th>CPF</Th>
                          <Th>Telefone</Th>
                          <Th>Convênio</Th>
                        </Tr>
                      </THead>
                      <TBody>
                        {pacientes.map((paciente) => (
                          <Tr key={paciente.id} className="cursor-pointer" onClick={() => setEditing(paciente)}>
                            <Td className="font-semibold">{paciente.nome}</Td>
                            <Td>{maskCpf(paciente.cpf)}</Td>
                            <Td>{paciente.telefone ?? "—"}</Td>
                            <Td>{paciente.convenio?.nome ?? "Particular"}</Td>
                          </Tr>
                        ))}
                      </TBody>
                    </Table>
                  </TableContainer>
                </div>
              </>
            )}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>{title}</CardTitle>
          </CardHeader>
          <CardContent>
            <form className="space-y-3" onSubmit={onSubmit}>
              <div>
                <Label>Nome completo</Label>
                <Input {...form.register("nome")} />
              </div>
              <div className="grid gap-3 sm:grid-cols-2">
                <div>
                  <Label>Data de nascimento</Label>
                  <Input type="date" {...form.register("data_nascimento")} />
                </div>
                <div>
                  <Label>Sexo</Label>
                  <Select {...form.register("sexo")}>
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                    <option value="O">Outro</option>
                  </Select>
                </div>
              </div>
              <div className="grid gap-3 sm:grid-cols-2">
                <div>
                  <Label>CPF</Label>
                  <Input {...form.register("cpf")} />
                </div>
                <div>
                  <Label>CNS</Label>
                  <Input {...form.register("cns")} />
                </div>
              </div>
              <div className="grid gap-3 sm:grid-cols-2">
                <div>
                  <Label>Telefone</Label>
                  <Input {...form.register("telefone")} />
                </div>
                <div>
                  <Label>Convênio</Label>
                  <Select {...form.register("convenio_id")}>
                    <option value="">Particular</option>
                    {lookups.convenios.map((item) => (
                      <option key={item.id} value={item.id}>
                        {item.nome}
                      </option>
                    ))}
                  </Select>
                </div>
              </div>
              <div>
                <Label>Alergias</Label>
                <Textarea rows={2} {...form.register("alergias")} />
              </div>
              <div>
                <Label>Comorbidades</Label>
                <Textarea rows={2} {...form.register("comorbidades")} />
              </div>
              <div>
                <Label>Observações</Label>
                <Textarea rows={2} {...form.register("observacoes")} />
              </div>
              <div className="grid grid-cols-2 gap-2">
                <Button disabled={saving} type="submit">
                  {saving ? "Salvando..." : editing ? "Atualizar" : "Cadastrar"}
                </Button>
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => {
                    setEditing(null);
                    form.reset({ nome: "", sexo: "O" });
                  }}
                >
                  Limpar
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
