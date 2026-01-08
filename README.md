# 📋 TasKer - Gerenciamento de Tarefas e Projetos

O **TasKer** é uma plataforma web intuitiva para gerenciamento de tarefas baseada na metodologia Kanban. Permite que utilizadores se registrem, criem quadros personalizados e gerenciem o fluxo de trabalho das suas tarefas (A Fazer, Em Andamento, Concluído) de forma visual.

> **Nota:** Este projeto foi desenvolvido e otimizado para ser hospedado gratuitamente na **InfinityFree**.

## 🚀 Funcionalidades

* **Autenticação Segura:** Sistema de Login e Registro de utilizadores com hash de senhas.
* **Dashboard:** Visão geral com estatísticas de tarefas (Total, A Fazer, Concluídas) e alertas de prazos.
* **Quadro Kanban:**
* Visualização de tarefas em colunas dinâmicas.
* Criação, edição e exclusão de colunas personalizadas.
* Movimentação de tarefas entre estados.


* **Gestão de Tarefas:** CRUD completo (Criar, Ler, Atualizar, Apagar) com prioridades e prazos.
* **Interface Responsiva:** Layout adaptável para desktop e dispositivos móveis.

## 🛠️ Tecnologias Utilizadas

* **Backend:** PHP (Nativo/Vanilla)
* **Base de Dados:** MySQL
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
* **Conexão:** PDO (PHP Data Objects)

---

## ⚙️ Instalação e Configuração

### 1. Preparação da Base de Dados

Independentemente do ambiente, você precisa criar a estrutura da base de dados. Execute o seguinte SQL no seu gestor (phpMyAdmin):

```sql
-- Criação da tabela de Utilizadores
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Criação da tabela de Colunas do Quadro
CREATE TABLE IF NOT EXISTS board_columns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Criação da tabela de Tarefas
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    project_name VARCHAR(255),
    deadline DATE,
    priority VARCHAR(50),
    status VARCHAR(50) DEFAULT 'todo',
    column_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (column_id) REFERENCES board_columns(id) ON DELETE SET NULL
);

```

### 2. Configuração da Conexão

O projeto inclui um ficheiro de exemplo para facilitar a configuração.

1. Vá até a pasta `config/`.
2. Renomeie o ficheiro `db_example.php` para **`db.php`**.
3. Abra o `db.php` e edite as credenciais conforme o seu ambiente abaixo:

#### 🏠 Ambiente Local (XAMPP/MAMP)

Ideal para desenvolvimento e testes no seu computador.

```php
// config/db.php (Local)
$host = 'localhost';
$dbname = 'tasker_db'; // O nome que você criou no phpMyAdmin local
$username = 'root';
$password = '';        // Geralmente vazio no XAMPP

```

#### ☁️ Ambiente de Produção (Recomendado: InfinityFree)

Este projeto foi testado e aprovado para rodar na InfinityFree.

1. Faça upload de todos os ficheiros para a pasta `htdocs` via FTP ou Gerenciador de Arquivos.
2. No painel da InfinityFree, obtenha as credenciais do MySQL (Host, User, Senha e Nome do Banco).
3. Edite o `config/db.php` **diretamente no servidor** (ou edite localmente e suba apenas este arquivo):

```php
// config/db.php (Produção - Exemplo InfinityFree)
$host = '#####.infinityfree.com'; // Exemplo de host
$dbname = 'if0_00000_tasker';   // Nome do banco fornecido por eles
$username = 'if0_777777';        // Usuário fornecido por eles
$password = 'SUA_SENHA_DO_CPANEL'; // Senha do painel/vPanel

```

---

## ⚠️ Fluxo de Desenvolvimento

Para garantir a integridade do projeto ao trabalhar com Local e Produção simultaneamente:

1. **Use o `db_example.php`:** Mantenha o `db.php` no seu `.gitignore` (se usar Git) para não vazar senhas ou sobrescrever a configuração de produção com a local (e vice-versa).
2. **Upload com Cuidado:** Ao enviar atualizações do seu PC para a InfinityFree, **evite** reenviar o ficheiro `config/db.php` se ele já estiver configurado corretamente lá, para não quebrar a conexão do site.

## 📂 Estrutura de Pastas

* `auth/`: Scripts de login e registro.
* `config/`: Conexão com o banco (`db.php` e `db_example.php`).
* `controllers/`: Lógica de negócio.
* `views/`: Interface do usuário (Dashboard, Kanban).
* `tasks/` & `columns/`: Processamento de dados.
* `assets/` (images/js/styles): Arquivos estáticos.

---

**Desenvolvido com PHP.**
