"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { ShieldCheck, Stethoscope } from "lucide-react";
import { toast } from "sonner";
import { api, apiErrorMessage } from "@/lib/api-client";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { useAuthStore } from "@/store/auth-store";
import type { User } from "@/types/api";

const schema = z.object({
  email: z.string().email("Informe um e-mail válido."),
  password: z.string().min(8, "Senha obrigatória."),
});

type LoginForm = z.infer<typeof schema>;

export default function LoginPage() {
  const router = useRouter();
  const { setSession } = useAuthStore();
  const [loading, setLoading] = useState(false);

  const form = useForm<LoginForm>({
    resolver: zodResolver(schema),
    defaultValues: {
      email: "",
      password: "",
    },
  });

  const onSubmit = form.handleSubmit(async (values) => {
    setLoading(true);
    try {
      const { data } = await api.post<{ token: string; user: User }>("/auth/login", values);
      setSession({ token: data.token, user: data.user });
      toast.success("Login realizado com sucesso.");
      router.replace("/dashboard");
    } catch (error) {
      toast.error(apiErrorMessage(error));
    } finally {
      setLoading(false);
    }
  });

  return (
    <div className="grid min-h-screen grid-cols-1 bg-background lg:grid-cols-2">
      <div className="relative hidden overflow-hidden bg-gradient-to-br from-primary via-[#0e3970] to-accent p-10 text-white lg:block">
        <div className="absolute right-8 top-8 rounded-full border border-white/25 px-4 py-2 text-xs font-semibold">MVP UniHosp</div>
        <div className="flex h-full flex-col justify-between">
          <div className="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15">
            <Stethoscope className="h-7 w-7" />
          </div>
          <div>
            <h1 className="font-display text-4xl font-semibold">Gestão hospitalar confiável, rápida e moderna.</h1>
            <p className="mt-4 max-w-md text-sm text-white/85">
              Fluxo completo de recepção, triagem, atendimento e painel TV em tempo real com foco em operação clínica.
            </p>
          </div>
          <p className="text-xs text-white/75">UniHosp • Plataforma operacional hospitalar</p>
        </div>
      </div>

      <div className="grid place-items-center p-5">
        <Card className="w-full max-w-md">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <ShieldCheck className="h-5 w-5 text-primary" />
              Entrar no UniHosp
            </CardTitle>
          </CardHeader>
          <CardContent>
            <form className="space-y-4" onSubmit={onSubmit}>
              <div>
                <Label htmlFor="email">E-mail</Label>
                <Input id="email" type="email" placeholder="admin@unihosp.local" {...form.register("email")} />
                {form.formState.errors.email ? (
                  <p className="mt-1 text-xs text-danger">{form.formState.errors.email.message}</p>
                ) : null}
              </div>
              <div>
                <Label htmlFor="password">Senha</Label>
                <Input id="password" type="password" placeholder="••••••••" {...form.register("password")} />
                {form.formState.errors.password ? (
                  <p className="mt-1 text-xs text-danger">{form.formState.errors.password.message}</p>
                ) : null}
              </div>
              <Button disabled={loading} fullWidth type="submit">
                {loading ? "Entrando..." : "Acessar"}
              </Button>
              <p className="text-xs text-muted-foreground">Usuário de seed: admin@unihosp.local / senha: UniHosp@123</p>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
