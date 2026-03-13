"use client";

import { useEffect, useState } from "react";
import { Clock3, Hospital } from "lucide-react";
import { useParams } from "next/navigation";
import { api } from "@/lib/api-client";
import { getEcho } from "@/lib/realtime";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

interface PainelResponse {
  painel: {
    nome: string;
    slug: string;
    mensagem_institucional?: string | null;
    forma_exibicao_paciente: string;
  };
  chamada_atual: {
    senha?: string | null;
    paciente?: string | null;
    setor?: string | null;
    sala?: string | null;
    profissional?: string | null;
    horario?: string | null;
  } | null;
  ultimas_chamadas: Array<{
    id: string;
    senha?: string | null;
    paciente?: string | null;
    setor?: string | null;
    sala?: string | null;
    horario?: string | null;
  }>;
}

export default function PainelTvPage() {
  const params = useParams<{ slug: string }>();
  const slug = params.slug;
  const [clock, setClock] = useState(new Date());
  const [data, setData] = useState<PainelResponse | null>(null);

  async function load(panelSlug: string) {
    const response = await api.get<PainelResponse>(`/paineis/publico/${panelSlug}`);
    setData(response.data);
  }

  useEffect(() => {
    if (!slug) return;
    const initialLoader = window.setTimeout(() => {
      load(slug).catch(() => undefined);
    }, 0);

    const clockTimer = setInterval(() => setClock(new Date()), 1000);
    const refreshTimer = setInterval(() => load(slug).catch(() => undefined), 5000);

    const echo = getEcho();
    if (echo) {
      echo.channel("unihosp.paineis").listen(".PainelAtualizado", () => {
        load(slug).catch(() => undefined);
      });
      echo.channel(`unihosp.painel.${slug}`).listen(".PainelAtualizado", () => {
        load(slug).catch(() => undefined);
      });
    }

    return () => {
      clearTimeout(initialLoader);
      clearInterval(clockTimer);
      clearInterval(refreshTimer);
      if (echo) {
        echo.leave("unihosp.paineis");
        echo.leave(`unihosp.painel.${slug}`);
      }
    };
  }, [slug]);

  return (
    <div className="min-h-screen bg-gradient-to-b from-[#03162f] via-[#062a53] to-[#041222] p-4 text-white md:p-8">
      <div className="mx-auto flex h-full max-w-[1500px] flex-col gap-6">
        <header className="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-white/15 bg-white/5 px-4 py-3">
          <div className="flex items-center gap-3">
            <div className="rounded-xl bg-white/10 p-2">
              <Hospital className="h-6 w-6 text-cyan-300" />
            </div>
            <div>
              <p className="text-xs uppercase tracking-wide text-white/70">Painel de chamadas</p>
              <p className="font-display text-xl font-semibold">{data?.painel.nome ?? "UniHosp"}</p>
            </div>
          </div>
          <div className="flex items-center gap-2 text-sm text-white/80">
            <Clock3 className="h-4 w-4" />
            {clock.toLocaleTimeString("pt-BR")}
          </div>
        </header>

        <Card className="border-white/20 bg-white/10 text-white">
          <CardContent className="py-8 text-center md:py-12">
            <p className="text-xs uppercase tracking-[0.25em] text-cyan-200/85">Chamada Atual</p>
            <p className="mt-2 font-display text-7xl font-semibold text-cyan-300 md:text-8xl">{data?.chamada_atual?.senha ?? "----"}</p>
            <p className="mt-3 text-2xl md:text-3xl">{data?.chamada_atual?.paciente ?? "Aguardando chamada..."}</p>
            <div className="mt-4 flex flex-wrap items-center justify-center gap-2">
              {data?.chamada_atual?.setor ? <Badge variant="info">{data.chamada_atual.setor}</Badge> : null}
              {data?.chamada_atual?.sala ? <Badge variant="warning">Sala {data.chamada_atual.sala}</Badge> : null}
              {data?.chamada_atual?.profissional ? <Badge>{data.chamada_atual.profissional}</Badge> : null}
            </div>
          </CardContent>
        </Card>

        <div className="grid gap-4 xl:grid-cols-[2fr,1fr]">
          <Card className="border-white/20 bg-white/10 text-white">
            <CardContent className="space-y-3 py-5">
              <p className="text-sm font-semibold uppercase tracking-wide text-cyan-200">Últimas chamadas</p>
              <div className="space-y-2">
                {data?.ultimas_chamadas?.slice(0, 8).map((item) => (
                  <div key={item.id} className="flex items-center justify-between rounded-xl border border-white/10 bg-black/20 px-3 py-2">
                    <div>
                      <p className="font-display text-2xl font-semibold text-cyan-300">{item.senha}</p>
                      <p className="text-sm text-white/85">{item.paciente}</p>
                    </div>
                    <div className="text-right text-sm text-white/70">
                      <p>{item.setor}</p>
                      <p>{item.sala ? `Sala ${item.sala}` : ""}</p>
                      <p>{item.horario}</p>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          <Card className="border-white/20 bg-white/10 text-white">
            <CardContent className="space-y-3 py-5">
              <p className="text-sm font-semibold uppercase tracking-wide text-cyan-200">Mensagem institucional</p>
              <p className="rounded-xl border border-white/10 bg-black/20 p-3 text-sm">
                {data?.painel.mensagem_institucional ?? "Cuide-se. Sua saúde é prioridade para o UniHosp."}
              </p>
              <p className="text-xs text-white/70">Modo de exibição: {data?.painel.forma_exibicao_paciente ?? "senha"}</p>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
