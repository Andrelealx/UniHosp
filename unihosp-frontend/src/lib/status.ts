type BadgeVariant = "default" | "success" | "warning" | "danger" | "info";

export function senhaStatusVariant(status?: string): BadgeVariant {
  switch (status) {
    case "aguardando":
      return "warning";
    case "chamado":
      return "info";
    case "em_atendimento":
      return "default";
    case "finalizado":
      return "success";
    case "cancelado":
    case "ausente":
      return "danger";
    case "encaminhado":
      return "info";
    default:
      return "default";
  }
}
