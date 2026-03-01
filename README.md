# 🏥 OrtoTraceability - Gestão de Rastreabilidade OPME (MVP Tecnológico)

O **OrtoTraceability** é um MVP (Minimum Viable Product) desenvolvido em **Laravel 12**, que demonstra uma arquitetura funcional para rastreabilidade unitária de materiais OPME (Órteses, Próteses e Materiais Especiais) em ambiente hospitalar.

Este projeto foi desenvolvido como prova de conceito técnica e arquitetural, inspirado na experiência profissional do autor com sistemas de rastreabilidade hospitalar, porém não replica, copia ou expõe qualquer propriedade intelectual, regra de negócio proprietária ou dado sensível da empresa onde atuou.

O foco é demonstrar domínio técnico sobre:
- **Modelagem de rastreabilidade unitária**
- **Controle de estoque hospitalar**
- **Auditoria imutável**
- **Fluxo cirúrgico vinculado a materiais**
- **Conformidade e segurança da informação**

## 🎯 Objetivo do Projeto

Este MVP demonstra como um sistema hospitalar pode:
- Garantir rastreabilidade completa por lote e número de série
- Reduzir riscos de uso de materiais vencidos
- Registrar trilha de auditoria para compliance
- Melhorar governança e controle de OPME
- Estruturar um fluxo seguro entre estoque e sala cirúrgica

## 🚀 Principais Funcionalidades

- **Rastreabilidade Unitária:** Controle rigoroso de lote, número de série e validade por material.
- **Vínculo Cirúrgico:** Agendamento de cirurgias com vinculação inteligente de materiais em tempo real.
- **Trilha de Auditoria Imutável:** Registro automático de todas as operações críticas (quem fez, o quê e quando).
- **Gestão de Estoque:** Painel visual com alertas de vencimento (30 dias) e bloqueio automático de itens vencidos.
- **Autenticação Segura:** Sistema de login e cadastro integrado para profissionais autorizados.

## 🛠️ Stack Tecnológica

- **Framework:** Laravel 12
- **Banco de Dados:** SQLite (Desenvolvimento)
- **Frontend:** Blade, Tailwind CSS (Design Premium) e Alpine.js (Interatividade)
- **Segurança:** Laravel Breeze (Auth), CSRF Protection e Trait de Auditoria customizado.

## 📦 Como Instalar

1. **Clonar o Repositório:**
   ```bash
   git clone https://github.com/Gabrielz11/OrtoTraceability.git
   cd OrtoTraceability
   ```

2. **Instalar Dependências:**
   ```bash
   composer install
   npm install
   ```

3. **Configuração do Ambiente:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Banco de Dados:**
   ```bash
   # Crie o arquivo do banco de dados (se estiver usando SQLite)
   touch database/database.sqlite
   
   # Rode as migrações e o seeder de demo
   php artisan migrate --seed --class=SystemSeeder
   ```

## 🔐 Acesso de Demonstração

O sistema já vem pré-configurado com dados de teste através do seeder:
- **Admin:** `admin@hospital.com`
- **Senha:** `password`

---
Desenvolvido com foco em **Qualidade & Rastreabilidade Hospitalar**.
