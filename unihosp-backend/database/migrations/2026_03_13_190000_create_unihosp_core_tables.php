<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('convenios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');
            $table->string('codigo')->nullable()->unique();
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['nome', 'deleted_at']);
        });

        Schema::create('setores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');
            $table->string('codigo')->unique();
            $table->string('tipo', 40)->default('recepcao')->index();
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['nome', 'deleted_at']);
        });

        Schema::create('salas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('setor_id')->constrained('setores')->cascadeOnUpdate();
            $table->string('nome');
            $table->string('codigo');
            $table->integer('ordem')->default(1);
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['setor_id', 'codigo', 'deleted_at']);
        });

        Schema::create('filas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('setor_id')->constrained('setores')->cascadeOnUpdate();
            $table->string('nome');
            $table->string('codigo')->unique();
            $table->string('tipo', 40)->index();
            $table->integer('ordem')->default(1)->index();
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['setor_id', 'nome', 'deleted_at']);
        });

        Schema::create('pacientes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('prontuario_codigo')->unique();
            $table->string('nome')->index();
            $table->string('nome_social')->nullable();
            $table->date('data_nascimento')->nullable()->index();
            $table->string('sexo', 1)->nullable()->index();
            $table->string('nome_mae')->nullable()->index();
            $table->string('cpf', 14)->nullable()->unique();
            $table->string('cns', 32)->nullable()->unique();
            $table->string('rg', 20)->nullable()->index();
            $table->string('telefone', 20)->nullable()->index();
            $table->string('telefone_secundario', 20)->nullable();
            $table->string('email')->nullable()->index();
            $table->json('endereco')->nullable();
            $table->foreignUuid('convenio_id')->nullable()->constrained('convenios')->nullOnDelete()->cascadeOnUpdate();
            $table->string('responsavel_nome')->nullable();
            $table->string('responsavel_telefone', 20)->nullable();
            $table->text('alergias')->nullable();
            $table->text('comorbidades')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('paineis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->string('tipo', 40)->index();
            $table->foreignUuid('setor_id')->nullable()->constrained('setores')->nullOnDelete()->cascadeOnUpdate();
            $table->text('mensagem_institucional')->nullable();
            $table->string('forma_exibicao_paciente', 50)->default('senha')->index();
            $table->string('logo_url')->nullable();
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('senhas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo')->unique();
            $table->unsignedInteger('numero_sequencial')->index();
            $table->date('data_referencia')->index();
            $table->string('tipo_atendimento', 40)->index();
            $table->string('prioridade', 20)->default('normal')->index();
            $table->foreignUuid('paciente_id')->constrained('pacientes')->cascadeOnUpdate();
            $table->foreignUuid('fila_id')->constrained('filas')->cascadeOnUpdate();
            $table->foreignUuid('setor_id')->constrained('setores')->cascadeOnUpdate();
            $table->foreignUuid('sala_id')->nullable()->constrained('salas')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('encaminhada_para_fila_id')->nullable()->constrained('filas')->nullOnDelete()->cascadeOnUpdate();
            $table->string('status', 30)->default('aguardando')->index();
            $table->text('observacoes_iniciais')->nullable();
            $table->foreignId('emitida_por_user_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('chamada_por_user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('finalizada_por_user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamp('horario_emissao')->index();
            $table->timestamp('horario_chamada')->nullable()->index();
            $table->timestamp('horario_finalizacao')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fila_id', 'status', 'prioridade']);
            $table->index(['setor_id', 'status', 'horario_emissao']);
        });

        Schema::create('chamadas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('senha_id')->constrained('senhas')->cascadeOnUpdate();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignUuid('setor_id')->constrained('setores')->cascadeOnUpdate();
            $table->foreignUuid('sala_id')->nullable()->constrained('salas')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('painel_id')->nullable()->constrained('paineis')->nullOnDelete()->cascadeOnUpdate();
            $table->string('tipo', 30)->default('chamada')->index();
            $table->string('status', 30)->default('emitida')->index();
            $table->unsignedSmallInteger('repeticao')->default(0);
            $table->text('mensagem')->nullable();
            $table->timestamp('chamado_em')->index();
            $table->timestamps();

            $table->index(['setor_id', 'chamado_em']);
        });

        Schema::create('triagens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('senha_id')->unique()->constrained('senhas')->cascadeOnUpdate();
            $table->foreignUuid('paciente_id')->constrained('pacientes')->cascadeOnUpdate();
            $table->foreignId('profissional_id')->constrained('users')->cascadeOnUpdate();
            $table->string('pressao_arterial', 20)->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->unsignedSmallInteger('saturacao')->nullable();
            $table->unsignedSmallInteger('frequencia_cardiaca')->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('altura', 4, 2)->nullable();
            $table->decimal('glicemia', 6, 2)->nullable();
            $table->string('classificacao_risco', 20)->nullable()->index();
            $table->text('observacoes')->nullable();
            $table->foreignUuid('encaminhar_fila_id')->nullable()->constrained('filas')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamp('iniciado_em')->nullable()->index();
            $table->timestamp('finalizado_em')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('prontuarios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paciente_id')->unique()->constrained('pacientes')->cascadeOnUpdate();
            $table->text('resumo_clinico')->nullable();
            $table->text('alergias')->nullable();
            $table->text('comorbidades')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamp('ultimo_atendimento_em')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('atendimentos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('senha_id')->constrained('senhas')->cascadeOnUpdate();
            $table->foreignUuid('paciente_id')->constrained('pacientes')->cascadeOnUpdate();
            $table->foreignId('medico_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignUuid('triagem_id')->nullable()->constrained('triagens')->nullOnDelete()->cascadeOnUpdate();
            $table->text('queixa_principal')->nullable();
            $table->text('hipotese_diagnostica')->nullable();
            $table->string('cid_codigo', 20)->nullable()->index();
            $table->text('conduta')->nullable();
            $table->text('prescricao_resumo')->nullable();
            $table->string('status', 30)->default('em_atendimento')->index();
            $table->timestamp('iniciado_em')->nullable()->index();
            $table->timestamp('finalizado_em')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['medico_id', 'status', 'iniciado_em']);
        });

        Schema::create('evolucoes_medicas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('prontuario_id')->constrained('prontuarios')->cascadeOnUpdate();
            $table->foreignUuid('atendimento_id')->nullable()->constrained('atendimentos')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('medico_id')->constrained('users')->cascadeOnUpdate();
            $table->text('descricao');
            $table->string('cid_codigo', 20)->nullable()->index();
            $table->timestamp('data_registro')->index();
            $table->timestamps();
        });

        Schema::create('prescricoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('atendimento_id')->constrained('atendimentos')->cascadeOnUpdate();
            $table->foreignUuid('paciente_id')->constrained('pacientes')->cascadeOnUpdate();
            $table->foreignId('medico_id')->constrained('users')->cascadeOnUpdate();
            $table->text('conteudo');
            $table->text('orientacoes')->nullable();
            $table->timestamp('emitida_em')->index();
            $table->timestamps();
        });

        Schema::create('auditoria_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('acao', 60)->index();
            $table->string('modulo', 80)->index();
            $table->string('entidade', 120)->nullable();
            $table->string('entidade_id', 80)->nullable();
            $table->string('metodo', 10)->index();
            $table->string('rota')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['entidade', 'entidade_id']);
            $table->index(['modulo', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria_logs');
        Schema::dropIfExists('prescricoes');
        Schema::dropIfExists('evolucoes_medicas');
        Schema::dropIfExists('atendimentos');
        Schema::dropIfExists('prontuarios');
        Schema::dropIfExists('triagens');
        Schema::dropIfExists('chamadas');
        Schema::dropIfExists('senhas');
        Schema::dropIfExists('paineis');
        Schema::dropIfExists('pacientes');
        Schema::dropIfExists('filas');
        Schema::dropIfExists('salas');
        Schema::dropIfExists('setores');
        Schema::dropIfExists('convenios');
    }
};
