# BRIEF

## Problema
Hospitais e distribuidoras de materiais cirúrgicos precisam controlar e provar rastreabilidade completa de OPME implantável por unidade, reduzindo risco (vencimento, uso indevido, recall) e garantindo auditoria.

## Solução
Sistema monolítico server-side (Laravel + MySQL + Blade) para:
- Cadastrar materiais por unidade rastreável
- Cadastrar cirurgias
- Vincular múltiplos materiais por cirurgia com validações de risco
- Registrar auditoria imutável para todas as ações
- Dashboard com métricas operacionais e de risco

## Público
Administração hospitalar / Qualidade / Auditoria (usuário único Admin no MVP).

## Diferencial
- Rastreabilidade unitária (lote obrigatório, série opcional)
- Trilha de auditoria completa (before/after + eventos de vínculo)
- Bloqueios automáticos de risco (vencido, status inválido, cirurgia cancelada)

## Modelo de Negócio
Licenciamento SaaS ou On-Premise por hospital ou empresa distribuidora de materiais.

## Métricas de Sucesso
- % de materiais com rastreabilidade completa
- Número de materiais próximos do vencimento
- Tempo para localizar impacto de um lote
- Número de bloqueios preventivos de risco