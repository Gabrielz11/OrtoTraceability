# PRD — Sistema Hospitalar de Traceability OPME

## 1. Visão Geral

Sistema para controle e rastreabilidade de materiais implantáveis (OPME), focado em:

- Controle de estoque por unidade
- Vínculo material ⇄ cirurgia
- Auditoria completa e imutável
- Visibilidade operacional via dashboard

Stack:
Laravel 12 + MySQL + Blade + Tailwind + Alpine.js  
Arquitetura: Monolito simples, server-side first.

---

## 2. Personas

### Admin
- Responsável por cadastro, vínculo e auditoria.
- Precisa comprovar cadeia de custódia e uso.

---

## 3. Modelo de Domínio

### 3.1 MaterialItem

Representa uma unidade física rastreável.

Campos:
- id
- lote (obrigatório)
- numero_serie (opcional, único quando informado)
- validade (obrigatório)
- fabricante (obrigatório)
- status (enum):
  - em_estoque
  - reservado
  - implantado_usado
  - descartado
  - devolvido_ao_fornecedor
- observacoes
- timestamps
- soft delete

Regras:
- Número de série único
- Não permitir uso se vencido
- Não permitir marcar como implantado sem vínculo

---

### 3.2 Surgery

Campos:
- id
- data_hora
- hospital
- medico
- paciente
- status:
  - agendada
  - realizada
  - cancelada
- observacoes
- timestamps
- soft delete

---

### 3.3 SurgeryMaterial

Pivot entre cirurgia e material.

Campos:
- id
- surgery_id
- material_item_id
- acao:
  - reservado
  - usado
- timestamps

Regras:
- Um material só pode estar vinculado ativamente a uma cirurgia
- Não permitir desvincular material já usado

---

### 3.4 AuditLog

Campos:
- id
- actor_user_id
- action (create, update, delete, link, unlink, status_change)
- entity_type
- entity_id
- before (json)
- after (json)
- metadata (json)
- created_at

Regras:
- Nunca atualizar registros de auditoria
- Registrar todas ações críticas

---

## 4. Requisitos Funcionais

### RF01 - Autenticação
Login básico para 1 usuário Admin.

### RF02 - CRUD Material
- Criar, editar, listar, visualizar, soft delete
- Filtros por status, lote, validade, fabricante

### RF03 - CRUD Cirurgia
- Criar, editar, listar, visualizar
- Filtro por período e status

### RF04 - Vinculação
- Vincular múltiplos materiais
- Atualizar status automaticamente

### RF05 - Uso
- Marcar material como usado
- Validar vencimento
- Validar status

### RF06 - Auditoria
- Registrar create/update/delete/link/unlink/status_change
- Tela global de auditoria
- Aba histórico em material e cirurgia

### RF07 - Dashboard
- KPIs de estoque
- Próximos do vencimento
- Uso por período
- Últimas ações

---

## 5. Requisitos Não Funcionais

- Server-side first
- Transações para vínculo/uso
- Index em lote, série e validade
- CSRF ativo
- Hash padrão Laravel para senha