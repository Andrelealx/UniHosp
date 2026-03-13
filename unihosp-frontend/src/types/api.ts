export type RoleName = "administrador" | "recepcao" | "enfermagem" | "medico";

export interface User {
  id: number;
  name: string;
  email: string;
  cpf?: string | null;
  phone?: string | null;
  is_active: boolean;
  roles: RoleName[];
  last_login_at?: string | null;
}

export interface Setor {
  id: string;
  nome: string;
  codigo: string;
  tipo: string;
  ativo: boolean;
}

export interface Fila {
  id: string;
  nome: string;
  codigo: string;
  tipo: string;
  ordem: number;
  setor_id: string;
}

export interface Sala {
  id: string;
  nome: string;
  codigo: string;
  setor_id: string;
}

export interface Convenio {
  id: string;
  nome: string;
  codigo?: string | null;
}

export interface Paciente {
  id: string;
  prontuario_codigo: string;
  nome: string;
  cpf?: string | null;
  cns?: string | null;
  rg?: string | null;
  telefone?: string | null;
  data_nascimento?: string | null;
  sexo?: "M" | "F" | "O" | null;
  nome_mae?: string | null;
  convenio_id?: string | null;
  convenio?: Convenio | null;
  alergias?: string | null;
  comorbidades?: string | null;
  observacoes?: string | null;
}

export interface Senha {
  id: string;
  codigo: string;
  tipo_atendimento: string;
  prioridade: "normal" | "prioritario" | "urgente";
  status: "aguardando" | "chamado" | "em_atendimento" | "ausente" | "finalizado" | "cancelado" | "encaminhado";
  paciente_id: string;
  paciente?: Paciente;
  fila_id: string;
  fila?: Fila;
  setor_id: string;
  setor?: Setor;
  sala_id?: string | null;
  horario_emissao?: string | null;
  horario_chamada?: string | null;
  horario_finalizacao?: string | null;
}

export interface Chamada {
  id: string;
  senha_id: string;
  senha?: Senha;
  tipo: string;
  status: string;
  mensagem?: string | null;
  chamado_em: string;
}

export interface Triagem {
  id: string;
  senha_id: string;
  paciente_id: string;
  classificacao_risco?: string | null;
  pressao_arterial?: string | null;
  temperatura?: number | null;
  saturacao?: number | null;
  frequencia_cardiaca?: number | null;
  peso?: number | null;
  altura?: number | null;
  glicemia?: number | null;
  observacoes?: string | null;
}

export interface Atendimento {
  id: string;
  senha_id: string;
  paciente_id: string;
  medico_id: number;
  hipotese_diagnostica?: string | null;
  cid_codigo?: string | null;
  conduta?: string | null;
  status: string;
}

export interface Painel {
  id: string;
  nome: string;
  slug: string;
  tipo: "recepcao" | "triagem" | "medico";
  setor_id?: string | null;
  mensagem_institucional?: string | null;
  forma_exibicao_paciente: "senha" | "senha_iniciais" | "senha_primeiro_nome";
  ativo: boolean;
}
