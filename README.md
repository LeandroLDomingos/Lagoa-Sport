# 🚀 Lagoa Sport

## 📌 Tecnologias Utilizadas

- **Laravel 12** (Backend)
- **Inertia.js** (Comunicação frontend/backend)
- **Vue.js 3** (Interface)
- **Tailwind CSS** (Estilização)
- **TypeScript** (Frontend)
- **Pinia** (Gerenciamento de estado)
- **ShadCN-Vue** (Componentes UI)
- **MySQL/PostgreSQL** (Banco de dados)

---

## ⚙️ **Instalação e Configuração**

### 1️⃣ **Clone o repositório**
```sh
git clone https://github.com/seu-usuario/seu-projeto.git
cd seu-projeto
```

### 2️⃣ **Instale as dependências**
```sh
composer install
npm install
```

### 3️⃣ **Configuração do ambiente**
Crie o arquivo `.env` a partir do `.env.example` e configure seu banco de dados:
```sh
cp .env.example .env
```
No arquivo `.env`, configure:
```env
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 4️⃣ **Gere a chave da aplicação**
```sh
php artisan key:generate
```

### 5️⃣ **Execute as migrações e seeders**
```sh
php artisan migrate --seed
```
O sistema já virá com um **usuário administrador** padrão.

### 6️⃣ **Compilar os assets**
```sh
npm run dev
```

---

## 🚀 **Executando o Projeto**
**Backend**:
```sh
php artisan serve
```

**Frontend** (modo desenvolvimento):
```sh
npm run dev
```

---

## 🔐 **Gerenciamento de Usuários e Permissões**
O sistema possui um middleware de controle de acesso (**ACLMiddleware**) que verifica as permissões dos usuários.

Os usuários podem ter os seguintes **status**:
- `pending` → Aguarda aprovação.
- `active` → Usuário aprovado.
- `is_analising` → Usuário em análise.
- `inactive` → Conta expirada (6 meses sem aprovação).
- `blocked` → Usuário bloqueado.

Os usuários **pendentes** devem enviar os seguintes documentos:
1. **Identidade (RG ou CNH)**
2. **Comprovante de Residência**

Após a aprovação de um administrador, eles terão acesso ao sistema.

---

## 📌 **TODO (Próximas Melhorias)**
✅ Upload de documentos  
✅ Middleware de status do usuário  
⬜ Notificações por e-mail  

---


🔥 **Feito com ❤️ e Laravel!** 🚀

