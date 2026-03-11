# Documentação Técnica - Wallet Cobuccio

Esta documentação detalha a arquitetura, os processos de negócio e as decisões técnicas tomadas no desenvolvimento do sistema de carteira financeira.

## 🏗️ Arquitetura e Padrões de Projeto

O sistema foi construído seguindo princípios de **Clean Architecture** e **SOLID**, garantindo que a lógica de negócio seja independente de frameworks e drivers externos.

### Camadas do Sistema

1.  **Apresentação (Livewire 3 + Blade):**
    *   Componentes reativos que gerenciam o estado da UI.
    *   Utiliza Traits (`InteractionWithWallet`) para compartilhar lógica entre Dashboard Admin e Cliente.
2.  **Serviços (`App\Services`):**
    *   Contém a "Core Business Logic".
    *   Responsável por orquestrar operações complexas, validações de saldo e fluxos de estorno.
3.  **Contratos (`App\Contracts`):**
    *   Interfaces que definem o comportamento esperado das implementações.
    *   Garante o desacoplamento entre a lógica e a persistência.
4.  **Repositórios (`App\Repositories`):**
    *   Implementação concreta do acesso ao banco de dados usando Eloquent.
5.  **Entidades (`App\Models`):**
    *   Representação dos dados e relacionamentos (ORM).

---

## 💰 Motor Financeiro e Integridade de Dados

### Precisão Monetária
Para evitar erros de arredondamento de ponto flutuante, todos os cálculos são realizados em **Centavos** utilizando o tipo `BigInteger` no banco de dados.
*   `R$ 10,50` é armazenado como `1050`.

### Atomicidade (ACID)
Todas as operações que envolvem movimentação de dinheiro (Depósitos, Transferências e Estornos) são encapsuladas em **Transações de Banco de Dados**.
```php
DB::transaction(function () {
    // 1. Deduzir saldo
    // 2. Adicionar saldo
    // 3. Registrar transação
});
```
Se qualquer etapa falhar, o estado anterior é restaurado automaticamente.

### Fluxo de Estorno (Reversibilidade)
Conforme requisito do desafio, o sistema permite a reversão de:
*   **Transferências:** O saldo volta do destinatário para o remetente.
*   **Depósitos:** O saldo é retirado da conta do usuário (estorno de entrada externa).

---

## 🔒 Segurança e Governança

### Role-Based Access Control (RBAC)
O acesso é controlado por um Middleware customizado que valida o campo `role` no modelo `User`:
*   `admin`: Gestão total de usuários e aprovação de estornos.
*   `client`: Operações de carteira e extrato pessoal.

### Políticas de Senha e Proteção
*   **Rate Limiting:** Proteção contra Brute Force no Login.
*   **Password Rules:** Validação de complexidade e verificação contra vazamentos conhecidos (`uncompromised`).
*   **Session Security:** Regeneração de ID de sessão em eventos críticos (Login/Register).

---

## 🏗️ Implementação e Injeção de Dependência

### Repository Pattern
Utilizamos o padrão **Repository** para abstrair a persistência. Isso significa que, se o sistema precisar trocar de MySQL para MongoDB ou usar uma API externa no futuro, as classes de serviço permanecem inalteradas.
*   **Contratos:** `App\Contracts\UserRepositoryInterface`, `App\Contracts\TransactionRepositoryInterface`.
*   **Bindings:** Gerenciados no `AppServiceProvider`.

### Injeção de Dependência
Nossas classes de serviço (`TransactionService`) não instanciam dependências manualmente. Elas dependem das abstrações (Interfaces), o que facilita o uso de **Mocks** em testes unitários.

```php
public function __construct(
    protected TransactionRepositoryInterface $transactionRepository,
    protected UserRepositoryInterface $userRepository
) {}
```
---

## 🔄 Fluxos de Negócio

### 1. Transferência entre Usuários
1.  Início da transação DB.
2.  Validação de saldo do remetente (BigInteger).
3.  Débito do valor no remetente.
4.  Crédito do valor no destinatário.
5.  Registro da transação na tabela `transactions`.
6.  Commit ou Rollback total.

### 2. Estorno (Reversal)
1.  Solicitação pelo usuário (com motivo obrigatório).
2.  Aprovação manual por um Admin.
3.  Simulação de uma transação inversa (Rollback lógico).
4.  Geração de uma transação do tipo `reversal` ou `transfer` vinculada à original via `related_transaction_id`.

---

## 📊 Governança e Audit
O sistema foi projetado para ser 100% auditável. Cada movimentação financeira gera um registro imutável no histórico.

### Comando de Auditoria de Integridade
Implementamos uma ferramenta de auditoria via console (`php artisan wallet:audit`) que executa as seguintes etapas:
1. **Recuperação Retroativa:** Para cada usuário, o sistema soma todas as transações de entrada (`deposit`, `transfer` como recebedor) e subtrai as saídas (`transfer` como remetente, `reversal` como remetente).
2. **Comparação de Estado:** O saldo calculado é comparado com o campo `balance` na tabela `users`.
3. **Resolução de Conflitos:** Através da flag `--fix`, o administrador pode sincronizar o saldo do usuário com o que consta no histórico de transações, garantindo que o sistema nunca perca a integridade financeira em caso de erro de sistema ou intervenção manual indevida.

```bash
php artisan wallet:audit --fix
```
---

## 🛠️ Manutenibilidade e Testes

### Testes Automatizados
O projeto utiliza **PHPUnit** para garantir a estabilidade das regras de negócio:
*   **Feature Tests:** Validam fluxos completos de transações, estornos e gestão de usuários.
*   **Unit Tests:** Testam lógicas puras como o `MoneyHelper` (conversão centavos/R$).
*   **Mocks:** Utilizados para isolar comportamentos de repositórios.

### Execução de Testes
```bash
php artisan test
```

---

## 🚀 Próximos Passos (Diferenciais Pendentes)
1.  **Observabilidade:** Exportar logs de auditoria para ferramentas externas (Datadog/NewRelic).
2.  **Agendamento Automático:** Executar a auditoria diariamente via `Task Scheduling`.
3.  **Interface Gráfica de Auditoria:** Permitir que o Admin visualize divergências diretamente no painel.
