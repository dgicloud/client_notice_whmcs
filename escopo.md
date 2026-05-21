# Escopo do Projeto — HAD Avisos ao Cliente para WHMCS

## 1. Objetivo

Criar um módulo addon para WHMCS que permita à equipe administrativa da HAD Cloud cadastrar, editar, segmentar e controlar avisos exibidos na área do cliente, sem necessidade de alterar código PHP a cada novo comunicado.

O módulo foi pensado para resolver principalmente estes cenários:

- avisos jurídicos e contratuais;
- comunicados sobre responsabilidade de backup;
- oferta de contratação do HAD Vault para clientes sem backup;
- comunicados de manutenção programada;
- avisos de fatura vencida;
- alertas internos na área logada do cliente;
- registros de ciência/aceite por cliente.

## 2. Nome do módulo

**HAD Avisos ao Cliente**

Pasta técnica:

```text
/modules/addons/had_client_notice/
```

## 3. Funcionalidades implementadas

### 3.1 Múltiplos avisos

O módulo permite cadastrar vários avisos independentes.

Cada aviso possui:

- status ativo/inativo;
- título;
- chave/versão;
- mensagem;
- formato de conteúdo;
- tipo de exibição;
- ícone;
- botões;
- regras de aceite;
- regras de segmentação;
- período de exibição;
- configurações visuais.

### 3.2 Chave/versão do aviso

Cada aviso possui uma chave, por exemplo:

```text
backup-v1
backup-v2
contrato-cloud-v1
manutencao-sp-v1
had-vault-oferta-v1
```

A chave controla se o aviso deve aparecer novamente para clientes que já tinham aceitado uma versão anterior.

Exemplo:

- Cliente aceitou `backup-v1`.
- A equipe altera a mensagem e muda a chave para `backup-v2`.
- O aviso passa a aparecer novamente.

### 3.3 Tipos de exibição

Foram implementados cinco formatos:

| Tipo | Uso recomendado |
|---|---|
| Modal central | Avisos importantes, jurídicos e obrigatórios |
| Banner superior | Manutenção, instabilidade ou comunicado operacional |
| Banner inferior | Avisos não críticos |
| Toast lateral | Aviso comercial ou lembrete leve |
| Painel interno | Card dentro da área do cliente |

### 3.4 Conteúdo customizável

O admin pode escolher entre:

- texto simples;
- HTML limitado.

O modo HTML limitado aceita tags simples como:

```html
<p>
<strong>
<ul>
<ol>
<li>
<a>
<br>
```

Por segurança, o módulo remove atributos perigosos como `onclick`, `onload`, `style` e links `javascript:`.

### 3.5 Botões

Cada aviso possui:

- botão principal, usado para aceite ou fechamento;
- botão secundário opcional com URL.

Exemplos:

```text
Botão principal: Li e estou ciente
Botão secundário: Conhecer HAD Vault
URL: https://seudominio.com.br/backup
```

### 3.6 Aviso obrigatório

O aviso pode ser configurado como obrigatório.

Quando obrigatório:

- o botão de fechar não é exibido;
- o cliente precisa clicar no botão principal;
- pode ser exigido um checkbox de confirmação.

Exemplo de checkbox:

```text
Declaro que li e estou ciente da política de backup da HAD Cloud.
```

### 3.7 Exibir uma vez por cliente/dispositivo

O módulo usa duas camadas de controle:

1. `localStorage` no navegador, para evitar repetição no mesmo dispositivo;
2. banco de dados, para registrar aceite por cliente logado.

Se a opção “exibir apenas uma vez” estiver ativa, clientes que já aceitaram não verão novamente o mesmo aviso, salvo se a chave/versão for alterada.

### 3.8 Agendamento

Cada aviso pode ter:

- data/hora inicial;
- data/hora final.

Exemplo:

```text
Exibir a partir de: 2026-05-25 08:00
Exibir até: 2026-05-28 23:59
```

Se os campos ficarem vazios, o aviso fica disponível enquanto estiver ativo.

### 3.9 Segmentação por produto/serviço

O módulo permite escolher produtos do WHMCS diretamente no admin.

Regras disponíveis:

| Regra | Descrição |
|---|---|
| Todos os clientes | Exibe para todos os clientes filtrados pelas demais regras |
| Possui produto selecionado | Exibe para clientes que possuem ao menos um produto marcado |
| Não possui produto selecionado | Exibe para clientes que não possuem os produtos marcados |
| Possui produto-alvo e não possui backup | Exibe para clientes com Cloud/VPS e sem HAD Vault |

A regra “possui produto-alvo e não possui backup” foi pensada especialmente para a HAD Cloud.

Exemplo:

- Produtos-alvo: Cloud Server, VPS, Cloud Privada.
- Produtos de backup: HAD Vault, Backup Cloud.
- Resultado: o aviso aparece somente para clientes que têm Cloud/VPS, mas não têm backup contratado.

### 3.10 Segmentação por status do serviço

É possível definir quais status de serviço entram na regra de segmentação:

- Active;
- Suspended;
- Pending;
- Terminated.

O padrão recomendado é considerar apenas `Active`.

### 3.11 Segmentação por grupo de cliente

É possível filtrar por grupos de clientes cadastrados no WHMCS.

Exemplos:

- clientes corporativos;
- revendedores;
- parceiros;
- clientes Cloud Privada;
- clientes estratégicos.

Se nenhum grupo for marcado, a regra de grupo é ignorada.

### 3.12 Segmentação por status do cliente

É possível filtrar por status do cliente:

- Active;
- Inactive;
- Closed.

Se nenhum status for marcado, a regra é ignorada.

### 3.13 Segmentação por fatura vencida

O módulo pode exibir aviso somente para clientes com fatura vencida.

A lógica usada é:

```text
status = Unpaid
duedate < data atual
```

### 3.14 Customização visual

O admin pode configurar:

- cor do cabeçalho;
- cor do botão principal;
- cor de destaque;
- cor do texto;
- largura máxima do aviso;
- ícone.

As cores padrão seguem a identidade visual sugerida para a HAD Cloud:

```text
Roxo: #4b1d95
Laranja: #f97316
Texto: #222222
```

### 3.15 Histórico de aceite

Quando o cliente clica no botão principal, o módulo registra:

- ID do aviso;
- ID do cliente;
- chave/versão do aviso;
- título do aviso no momento do aceite;
- hash SHA-256 do conteúdo;
- IP;
- user-agent;
- data/hora do aceite.

Esses registros ficam disponíveis no admin em:

```text
Addons > HAD Avisos ao Cliente > Ver aceites
```

## 4. Estrutura técnica

```text
modules/
└── addons/
    └── had_client_notice/
        ├── had_client_notice.php
        ├── hooks.php
        └── ajax.php

escopo.md
readme.md
```

## 5. Arquivos

### 5.1 had_client_notice.php

Arquivo principal do addon module.

Responsável por:

- registrar configuração do módulo;
- criar tabelas na ativação;
- renderizar a tela administrativa;
- salvar avisos;
- listar avisos;
- exibir histórico de aceites;
- aplicar regras de segmentação;
- gerar HTML, CSS e JavaScript dos avisos para a área do cliente.

### 5.2 hooks.php

Arquivo de hook do módulo.

Responsável por registrar o hook:

```php
ClientAreaFooterOutput
```

Esse hook injeta os avisos no rodapé das páginas da área do cliente.

### 5.3 ajax.php

Endpoint usado pelo JavaScript para registrar aceite no banco de dados.

Recebe:

```text
notice_id
```

E registra o aceite quando o cliente está logado.

## 6. Tabelas criadas

### 6.1 mod_had_client_notice_notices

Tabela que armazena os avisos.

Campos principais:

| Campo | Função |
|---|---|
| id | ID do aviso |
| enabled | Ativo/inativo |
| title | Título |
| notice_key | Chave/versão |
| message | Conteúdo |
| content_format | Texto ou HTML limitado |
| display_type | Modal, banner, toast ou painel |
| icon | Ícone |
| show_once | Exibir uma vez |
| only_logged | Somente logado |
| is_mandatory | Obrigatório |
| requires_checkbox | Exige checkbox |
| start_at | Início da exibição |
| end_at | Fim da exibição |
| product_rule | Regra por produto |
| target_product_ids | Produtos-alvo |
| backup_product_ids | Produtos de backup |
| invoice_rule | Regra de fatura |
| target_client_group_ids | Grupos de clientes |
| target_client_statuses | Status do cliente |
| visual fields | Cores e largura |

### 6.2 mod_had_client_notice_acceptances

Tabela que armazena os aceites.

Campos principais:

| Campo | Função |
|---|---|
| id | ID do registro |
| notice_id | ID do aviso |
| userid | ID do cliente |
| notice_key | Chave aceita |
| title_snapshot | Título no momento do aceite |
| message_hash | Hash do conteúdo |
| ip_address | IP do cliente |
| user_agent | Navegador/dispositivo |
| accepted_at | Data/hora do aceite |

## 7. Fluxo de uso

### 7.1 Criar aviso de backup

1. Acessar o admin do WHMCS.
2. Ir em `Addons > HAD Avisos ao Cliente`.
3. Criar novo aviso.
4. Tipo: `Modal central`.
5. Marcar `Aviso obrigatório`.
6. Marcar `Exigir checkbox`.
7. Regra de produto: `possui produto-alvo e não possui backup`.
8. Marcar produtos Cloud/VPS como alvo.
9. Marcar produtos HAD Vault como backup.
10. Salvar.

### 7.2 Criar aviso de manutenção

1. Criar novo aviso.
2. Tipo: `Banner superior`.
3. Definir data inicial e final.
4. Segmentar por produtos afetados.
5. Salvar.

### 7.3 Criar aviso comercial

1. Criar novo aviso.
2. Tipo: `Toast lateral`.
3. Colocar botão secundário apontando para landing page.
4. Segmentar clientes sem o produto ofertado.
5. Salvar.

## 8. Limitações conhecidas

- O HTML é limitado por segurança e não deve ser usado como editor visual completo.
- O registro de aceite depende do cliente estar logado para vincular ao `userid`.
- O módulo não substitui assinatura eletrônica formal de contrato em ferramentas como Autentique, Clicksign, ZapSign ou DocuSign.
- Para avisos juridicamente sensíveis, recomenda-se validar o texto com jurídico.
- O módulo não bloqueia navegação no servidor; ele obriga o clique no front-end. Para bloqueio rígido de acesso seria necessário implementar controle adicional por rota/página.

## 9. Próximas melhorias possíveis

- Exportação CSV dos aceites.
- Página de auditoria por cliente.
- Logs de visualização, não apenas aceite.
- Editor WYSIWYG no admin.
- Upload de imagem/banner por aviso.
- Integração com assinatura eletrônica.
- Segmentação por servidor, grupo de servidores ou tag personalizada.
- Controle por idioma.
- Templates prontos de aviso.
- Webhook interno para notificar equipe quando um cliente aceitar determinado aviso.

## 10. Critério de aceite do projeto

O projeto é considerado funcional quando:

- o módulo aparece em `Addon Modules`;
- a ativação cria as tabelas;
- o admin consegue criar e editar avisos;
- os avisos aparecem na área do cliente conforme as regras;
- o botão de aceite registra o aceite no banco;
- o histórico de aceites pode ser consultado no admin;
- avisos com chave já aceita não reaparecem para o mesmo cliente, salvo alteração da chave.


## Observação sobre hooks e includes

A versão 2.1 não depende de arquivos da v1. O pacote inclui um bootstrap em:

```text
includes/hooks/had_client_notice_popup.php
```

Esse arquivo apenas chama o hook oficial da própria versão 2 em:

```text
modules/addons/had_client_notice/hooks.php
```

A constante `HCN_POPUP_HOOK_REGISTERED` evita registro duplicado do hook se a instalação carregar hooks tanto pela pasta do addon quanto pela pasta global `includes/hooks`.



## Correção v2.1

A versão 2.1 adiciona proteção contra carregamento duplicado das funções principais do módulo, especialmente:

- `had_client_notice_config()`;
- `had_client_notice_activate()`;
- `had_client_notice_deactivate()`;
- `had_client_notice_output()`.

Essa correção resolve o erro fatal `Cannot redeclare had_client_notice_config()` em instalações onde o WHMCS carrega o arquivo principal do addon e também o hook de bootstrap no mesmo request.
