# HAD Avisos ao Cliente para WHMCS

## Estrutura do pacote corrigido

A versão 2.1 corrigida contém também o bootstrap em `includes/hooks/had_client_notice_popup.php`.

Estrutura esperada:

```text
modules/addons/had_client_notice/had_client_notice.php
modules/addons/had_client_notice/hooks.php
modules/addons/had_client_notice/ajax.php
includes/hooks/had_client_notice_popup.php
escopo.md
readme.md
```

O arquivo em `includes/hooks` **não usa os arquivos da v1**. Ele apenas carrega o hook da própria v2 em `modules/addons/had_client_notice/hooks.php`.

Há uma trava interna (`HCN_POPUP_HOOK_REGISTERED`) para evitar carregamento duplicado caso o WHMCS também carregue automaticamente o `hooks.php` do addon.


Módulo addon para WHMCS que permite criar avisos customizáveis na área do cliente, com segmentação por produto, status, grupo, fatura vencida, agendamento, tipos de exibição e registro de aceite.

## Recursos principais

- Múltiplos avisos cadastráveis pelo admin.
- Aviso ativo/inativo.
- Modal central, banner superior, banner inferior, toast lateral ou painel interno.
- Texto simples ou HTML limitado.
- Botão principal e botão secundário opcional.
- Aviso obrigatório.
- Checkbox obrigatório antes do aceite.
- Exibição uma vez por cliente/dispositivo.
- Registro de aceite no banco.
- Segmentação por produto contratado.
- Segmentação para clientes com Cloud/VPS sem backup.
- Segmentação por grupo de cliente.
- Segmentação por status do cliente.
- Segmentação por fatura vencida.
- Agendamento por data/hora inicial e final.
- Customização de cores e largura.

## Estrutura do pacote

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

## Instalação

### 1. Envie os arquivos para o WHMCS

Faça upload da pasta `modules` para a raiz da instalação do WHMCS.

A estrutura final deve ficar assim:

```text
/SEU_WHMCS/modules/addons/had_client_notice/had_client_notice.php
/SEU_WHMCS/modules/addons/had_client_notice/hooks.php
/SEU_WHMCS/modules/addons/had_client_notice/ajax.php
```

### 2. Ative o módulo no admin

No WHMCS, acesse:

```text
Configurações > Sistema > Addon Modules
```

Ou, dependendo da versão/tradução:

```text
Setup > Addon Modules
```

Localize:

```text
HAD Avisos ao Cliente
```

Clique em **Activate**.

Na ativação, o módulo cria automaticamente as tabelas:

```text
mod_had_client_notice_notices
mod_had_client_notice_acceptances
```

### 3. Permissões administrativas

Após ativar, marque quais grupos de administradores podem acessar o módulo.

Depois, o acesso ficará em:

```text
Addons > HAD Avisos ao Cliente
```

## Como criar um aviso

1. Acesse `Addons > HAD Avisos ao Cliente`.
2. Clique em `+ Novo aviso`.
3. Preencha título, chave e mensagem.
4. Escolha o tipo de exibição.
5. Defina se será obrigatório ou não.
6. Configure segmentação, se necessário.
7. Salve.

## Chave/versão do aviso

A chave controla a repetição do aviso.

Exemplos:

```text
backup-v1
backup-v2
contrato-cloud-v1
manutencao-v1
```

Se o cliente já aceitou `backup-v1`, ele não verá novamente essa mesma versão.

Para exibir novamente para todos, altere para:

```text
backup-v2
```

## Tipos de exibição

### Modal central

Recomendado para avisos importantes, como:

- política de backup;
- alteração contratual;
- responsabilidade do cliente;
- termo de ciência obrigatório.

### Banner superior

Recomendado para:

- manutenção programada;
- aviso de instabilidade;
- mudança operacional;
- prazo de migração.

### Banner inferior

Recomendado para avisos leves e não bloqueantes.

### Toast lateral

Recomendado para ofertas, lembretes e chamadas comerciais.

### Painel interno

Recomendado para exibir um card dentro da área do cliente sem bloquear a navegação.

## Segmentação por produto

O módulo lê os produtos cadastrados no WHMCS e permite marcar produtos diretamente no formulário.

### Todos os clientes

Exibe para todos os clientes que passarem pelas demais regras.

### Possui produto selecionado

Exibe apenas para quem possui ao menos um dos produtos marcados.

### Não possui produto selecionado

Exibe apenas para quem não possui os produtos marcados.

### Possui produto-alvo e não possui backup

Essa é a regra mais importante para a HAD Cloud.

Exemplo:

- Produtos-alvo: Cloud Server, VPS, Cloud Privada.
- Produtos de backup: HAD Vault, Backup Cloud.

Resultado:

```text
O aviso aparece somente para clientes que têm Cloud/VPS e não têm backup contratado.
```

## Exemplo: aviso obrigatório de backup

Configuração sugerida:

```text
Título: Aviso importante sobre backup
Chave: backup-v1
Tipo: Modal central
Aviso obrigatório: Sim
Exigir checkbox: Sim
Exibir uma vez: Sim
Somente logado: Sim
Regra de produto: Possui produto-alvo e não possui backup
Produtos-alvo: Cloud Server, VPS, Cloud Privada
Produtos de backup: HAD Vault
```

Texto sugerido:

```text
Os serviços de Cloud Server, VPS e máquinas virtuais da HAD Cloud não incluem backup automático dos dados, arquivos, sistemas, bancos de dados ou ambientes do cliente, salvo quando houver contratação específica de uma solução de backup.

A responsabilidade pela criação, validação e manutenção das rotinas de backup é do cliente.

Para maior segurança, recomendamos a contratação do HAD Vault ou a utilização de uma estratégia própria de backup.
```

Checkbox sugerido:

```text
Declaro que li e estou ciente da política de backup da HAD Cloud.
```

## Registro de aceite

Quando o cliente clica no botão principal, o módulo salva:

```text
ID do aviso
ID do cliente
Chave/versão do aviso
Título do aviso
Hash SHA-256 do conteúdo
IP
User-Agent
Data/hora do aceite
```

Para consultar:

```text
Addons > HAD Avisos ao Cliente > Ver aceites
```

Também é possível consultar os aceites de um aviso específico clicando no botão `Aceites` da listagem.

## Agendamento

Cada aviso pode ter:

```text
Exibir a partir de
Exibir até
```

Se os dois campos ficarem vazios, o aviso será exibido enquanto estiver ativo.

## Customização visual

O formulário permite configurar:

```text
Cor do cabeçalho
Cor do botão principal
Cor de destaque
Cor do texto
Largura máxima do aviso
Ícone
```

Cores padrão:

```text
Cabeçalho: #4b1d95
Botão principal: #4b1d95
Destaque: #f97316
Texto: #222222
```

## HTML limitado

O módulo aceita HTML limitado para mensagens mais estruturadas.

Exemplo:

```html
<p>Prezado cliente,</p>
<p>Informamos que os serviços de <strong>Cloud Server</strong> não incluem backup automático.</p>
<ul>
    <li>Backups devem ser contratados separadamente;</li>
    <li>A responsabilidade pelos dados é do cliente;</li>
    <li>Recomendamos o HAD Vault.</li>
</ul>
```

Por segurança, o módulo remove eventos JavaScript, estilos inline e links `javascript:`.

## Desinstalação

Ao desativar o módulo, as tabelas são preservadas para manter histórico de avisos e aceites.

Para remover completamente, exclua manualmente as tabelas somente se tiver certeza:

```sql
DROP TABLE mod_had_client_notice_acceptances;
DROP TABLE mod_had_client_notice_notices;
```

## Solução de problemas

### O módulo não aparece no menu Addons

Verifique se os arquivos estão no caminho correto:

```text
/modules/addons/had_client_notice/had_client_notice.php
```

Depois confirme se o módulo foi ativado em `Addon Modules` e se seu grupo administrativo tem permissão de acesso.

### O aviso não aparece para o cliente

Verifique:

- o aviso está ativo;
- o cliente está logado;
- a data inicial/final permite exibição;
- a segmentação por produto está correta;
- o cliente já aceitou a mesma chave/versão;
- a opção “exibir uma vez” está ativada;
- a chave precisa ser alterada para forçar nova exibição.

### O aceite não registra

Verifique:

- o cliente está logado;
- o arquivo `ajax.php` existe;
- o navegador não bloqueou a requisição;
- as tabelas foram criadas;
- o WHMCS está usando HTTPS corretamente.

### Quero que o aviso apareça novamente para todos

Altere a chave do aviso.

Exemplo:

```text
backup-v1
```

para:

```text
backup-v2
```

## Observação jurídica

Este módulo registra ciência/aceite operacional no WHMCS, mas não substitui uma assinatura eletrônica formal quando houver necessidade contratual mais robusta.

Para contratos maiores, Cloud Privada, SLA personalizado ou clientes corporativos estratégicos, recomenda-se usar uma plataforma de assinatura eletrônica e manter este módulo como reforço de comunicação dentro da área do cliente.


## Correção v2.1 - erro Cannot redeclare

Esta versão protege as funções principais do addon com `function_exists`, evitando fatal error quando o WHMCS carrega o arquivo do módulo mais de uma vez no mesmo request.

Erro corrigido:

```text
PHP Fatal error: Cannot redeclare had_client_notice_config()
```

### Procedimento recomendado de atualização

1. Substitua a pasta:

```text
/modules/addons/had_client_notice/
```

2. Substitua o arquivo:

```text
/includes/hooks/had_client_notice_popup.php
```

3. Remova hooks antigos da v1, se ainda existirem, por exemplo:

```text
/includes/hooks/had_popup_aviso_cliente.php
/includes/hooks/had_client_notice.php
```

4. Limpe o cache de template do WHMCS, se necessário.

A versão 2.1 não depende da v1.
