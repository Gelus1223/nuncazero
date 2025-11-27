O NuncaZero é um site desenvolvido para compra e venda de contas de jogos.
A plataforma permite que usuários anunciem contas de diversos jogos, incluindo descrição, progresso e preço.
Outros usuários podem pesquisar, visualizar detalhes e realizar a compra utilizando cartão cadastrado ou PIX.

O sistema conta com um design moderno em tema dark, focado em usabilidade e navegação fluida.

-Funcionalidades
-Usuários
Cadastro de usuário
Login com validação segura (password_hash e password_verify)
Sistema de sessão e autenticação
Bloqueio de funcionalidades para usuários deslogados

-Compra de Contas
Visualização da conta selecionada
Seleção de método de pagamento (PIX ou cartão)
Escolha de cartão cadastrado
Cadastro de um novo cartão diretamente na compra
Exclusão automática da conta após compra concluída

-Cartões
Listagem de cartões cadastrados pelo usuário
Cadastro de novos cartões
Seleção de cartão durante a compra

- Vendas
Cadastro de contas para venda
A conta é listada com:
Nome do jogo
Descrição
Progresso
Preço

- Pesquisa Inteligente
Search bar com filtro em tempo real
Jogos aparecem conforme o usuário digita

- Navegação
Botões de voltar no topo esquerdo
Menu reorganizado e intuitivo
Bloqueio de compra e venda quando o usuário não está logado

- Tecnologias Utilizadas
- Front-end
HTML5
CSS3 (tema escuro + verde neon)
JavaScript (jQuery para interações em tempo real)
- Back-end
PHP 7+
Sessions para autenticação
password_hash / password_verify

- Banco de Dados
MySQL

Tabelas:
usuario
jogo
contas_vend
cartao

 - Estrutura Básica do Projeto
/nuncazero
│── index.php
│── menu.php
│── login.php
│── cadastro.php
│── pesquisar.php
│── cadastrar_conta.php
│── compra.php
│── cartao.php
│── conexao.php
│── /css
│── /img

- Segurança

O sistema utiliza:
Sessions para autenticação
password_hash() para senhas
password_verify() no login
Prepared Statements (bind_param) para evitar SQL Injection

- Funcionalidades Futuras (Sugestões)

Recuperação de senha
Carrinho de compras
Histórico de transações
Sistema de avaliação do vendedor
Painel administrativo

- Licença

Este projeto pode ser utilizado para fins acadêmicos, pessoais ou aprendizado.

- Desenvolvedor

Projeto desenvolvido por Lucas Batista Spiller, com melhorias e organização assistida pela IA.
