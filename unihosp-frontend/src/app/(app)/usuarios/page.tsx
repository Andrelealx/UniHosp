"use client";

import { useEffect, useState } from "react";
import { toast } from "sonner";
import { api, apiErrorMessage } from "@/lib/api-client";
import { PageHeader } from "@/components/common/page-header";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select } from "@/components/ui/select";
import { Table, TableContainer, TBody, Td, Th, THead, Tr } from "@/components/ui/table";
import type { User } from "@/types/api";

const initialForm = {
  name: "",
  email: "",
  cpf: "",
  phone: "",
  password: "",
  password_confirmation: "",
  role: "recepcao",
};

export default function UsuariosPage() {
  const [users, setUsers] = useState<User[]>([]);
  const [roles, setRoles] = useState<string[]>([]);
  const [form, setForm] = useState(initialForm);
  const [saving, setSaving] = useState(false);

  async function load() {
    const [usersResponse, rolesResponse] = await Promise.all([
      api.get<{ data: User[] }>("/usuarios"),
      api.get<{ data: string[] }>("/usuarios/roles"),
    ]);
    setUsers(usersResponse.data.data);
    setRoles(rolesResponse.data.data);
  }

  useEffect(() => {
    load().catch((error) => toast.error(apiErrorMessage(error)));
  }, []);

  async function save() {
    setSaving(true);
    try {
      await api.post("/usuarios", {
        name: form.name,
        email: form.email,
        cpf: form.cpf,
        phone: form.phone,
        password: form.password,
        password_confirmation: form.password_confirmation,
        roles: [form.role],
      });
      toast.success("Usuário criado.");
      setForm(initialForm);
      await load();
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setSaving(false);
    }
  }

  return (
    <div className="space-y-4">
      <PageHeader title="Usuários e permissões" subtitle="Perfis mínimos: administrador, recepção, enfermagem e médico." />

      <div className="grid gap-4 xl:grid-cols-[1.2fr,1fr]">
        <Card>
          <CardHeader>
            <CardTitle>Usuários ativos</CardTitle>
          </CardHeader>
          <CardContent>
            <TableContainer>
              <Table>
                <THead>
                  <Tr>
                    <Th>Nome</Th>
                    <Th>E-mail</Th>
                    <Th>Perfis</Th>
                  </Tr>
                </THead>
                <TBody>
                  {users.map((user) => (
                    <Tr key={user.id}>
                      <Td className="font-semibold">{user.name}</Td>
                      <Td>{user.email}</Td>
                      <Td>{user.roles.join(", ")}</Td>
                    </Tr>
                  ))}
                </TBody>
              </Table>
            </TableContainer>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Novo usuário</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div>
              <Label>Nome</Label>
              <Input value={form.name} onChange={(e) => setForm((old) => ({ ...old, name: e.target.value }))} />
            </div>
            <div>
              <Label>E-mail</Label>
              <Input value={form.email} onChange={(e) => setForm((old) => ({ ...old, email: e.target.value }))} />
            </div>
            <div className="grid gap-3 sm:grid-cols-2">
              <div>
                <Label>CPF</Label>
                <Input value={form.cpf} onChange={(e) => setForm((old) => ({ ...old, cpf: e.target.value }))} />
              </div>
              <div>
                <Label>Telefone</Label>
                <Input value={form.phone} onChange={(e) => setForm((old) => ({ ...old, phone: e.target.value }))} />
              </div>
            </div>
            <div className="grid gap-3 sm:grid-cols-2">
              <div>
                <Label>Senha</Label>
                <Input type="password" value={form.password} onChange={(e) => setForm((old) => ({ ...old, password: e.target.value }))} />
              </div>
              <div>
                <Label>Confirmação</Label>
                <Input
                  type="password"
                  value={form.password_confirmation}
                  onChange={(e) => setForm((old) => ({ ...old, password_confirmation: e.target.value }))}
                />
              </div>
            </div>
            <div>
              <Label>Perfil</Label>
              <Select value={form.role} onChange={(e) => setForm((old) => ({ ...old, role: e.target.value }))}>
                {roles.map((role) => (
                  <option key={role} value={role}>
                    {role}
                  </option>
                ))}
              </Select>
            </div>
            <Button onClick={save} disabled={saving} fullWidth>
              {saving ? "Salvando..." : "Criar usuário"}
            </Button>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
