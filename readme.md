# HAD Avisos ao Cliente para WHMCS

Módulo addon para WHMCS que permite criar e exibir avisos customizáveis na área do cliente, com segmentação de público, agendamento, tipos de exibição e registro de aceite.

## O que o módulo faz

### Criação de avisos

O administrador cadastra avisos com título, mensagem (texto simples ou HTML limitado), tipo de exibição e configurações de comportamento. Cada aviso possui uma chave/versão que controla a repetição: ao alterar a chave, o aviso volta a ser exibido para todos os clientes.

### Tipos de exibição

- **Modal central** — janela sobreposta, recomendada para avisos importantes ou obrigatórios
- **Banner superior** — faixa no topo da página
- **Banner inferior** — faixa no rodapé da página
- **Toast lateral** — notificação flutuante lateral
- **Painel interno** — card integrado à área do cliente sem bloquear a navegação

### Controle de exibição

- Ativar ou desativar cada aviso individualmente
- Exibir uma única vez por cliente/dispositivo
- Tornar o aviso obrigatório (o cliente não consegue fechar sem interagir)
- Exigir marcação de checkbox antes do aceite
- Botão principal e botão secundário opcional

### Segmentação de público

- Todos os clientes
- Clientes que possuem determinado produto contratado
- Clientes que não possuem determinado produto
- Clientes que possuem produto-alvo (ex: Cloud/VPS) e não possuem produto de backup
- Clientes de um grupo específico
- Clientes com determinado status (ativo, suspenso, etc.)
- Clientes com fatura vencida

### Agendamento

Cada aviso pode ter data/hora de início e fim. Fora do intervalo configurado, o aviso não é exibido mesmo que esteja ativo.

### Registro de aceite

Quando o cliente confirma o aviso, o módulo registra:

- ID do aviso e do cliente
- Chave/versão do aviso
- Título e hash SHA-256 do conteúdo
- IP e User-Agent
- Data e hora do aceite

Os registros ficam disponíveis no painel administrativo em `Addons > HAD Avisos ao Cliente > Ver aceites`.

### Customização visual

Cada aviso pode ter cores e largura configuradas individualmente:

- Cor do cabeçalho
- Cor do botão principal
- Cor de destaque
- Cor do texto
- Largura máxima
- Ícone

### Segurança do conteúdo HTML

O módulo aceita HTML limitado nas mensagens e remove automaticamente eventos JavaScript, estilos inline e links `javascript:`.
