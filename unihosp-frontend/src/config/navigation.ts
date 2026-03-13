import {
  Activity,
  ClipboardList,
  FileClock,
  FileText,
  LayoutDashboard,
  MonitorPlay,
  Stethoscope,
  Ticket,
  Users,
  UserSquare2,
  Waves,
} from "lucide-react";

export const appNavigation = [
  { href: "/dashboard", label: "Dashboard", icon: LayoutDashboard },
  { href: "/pacientes", label: "Pacientes", icon: UserSquare2 },
  { href: "/recepcao", label: "Recepção", icon: ClipboardList },
  { href: "/filas", label: "Filas", icon: Waves },
  { href: "/senhas", label: "Senhas", icon: Ticket },
  { href: "/chamadas", label: "Chamadas", icon: FileClock },
  { href: "/triagem", label: "Triagem", icon: Activity },
  { href: "/atendimentos", label: "Atendimento Médico", icon: Stethoscope },
  { href: "/prontuarios", label: "Prontuário", icon: FileText },
  { href: "/relatorios", label: "Relatórios", icon: MonitorPlay },
  { href: "/paineis", label: "Painel TV", icon: MonitorPlay },
  { href: "/usuarios", label: "Usuários", icon: Users },
] as const;
