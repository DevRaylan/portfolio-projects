export const categories = ["Moradia", "Mercado", "Contas", "Outros"] as const;
export type Category = typeof categories[number];

export const residents = [
  { id: "voce", name: "Você" },
  { id: "ana", name: "Ana" },
  { id: "leo", name: "Léo" },
] as const;
export type ResidentId = typeof residents[number]["id"];

export type Dispute = {
  author: ResidentId;
  reason: string;
  description: string;
  status: "open" | "reviewed" | "resolved";
  evidence?: string;
  evidenceName?: string;
  reply?: string;
  followUps?: string[];
};

export type ExpenseHistory = {
  id: string;
  actor: ResidentId;
  action: string;
  createdAt: string;
};

export type SplitMode = "equal" | "custom";

export type Recurrence = {
  frequency: "monthly";
};

export type DeletionRecord = {
  actor: ResidentId;
  createdAt: string;
};

export type ExpenseData = {
  id: string;
  groupId?: string;
  title: string;
  cents: number;
  category: Category;
  date: string;
  payer: ResidentId;
  participants: ResidentId[];
  recurrence?: Recurrence;
  splitMode?: SplitMode;
  customShares?: Partial<Record<ResidentId, number>>;
  receipt?: string;
  paid: ResidentId[];
  confirmed?: ResidentId[];
  deleted?: DeletionRecord;
  dispute?: Dispute;
  history?: ExpenseHistory[];
};

export class Expense {
  constructor(private readonly data: ExpenseData) {
    if (!data.title.trim() || !Number.isSafeInteger(data.cents) || data.cents <= 0) {
      throw new Error("Informe uma descrição e um valor válido.");
    }
    if (!categories.includes(data.category) || !residents.some(({ id }) => id === data.payer)
      || !/^\d{4}-\d{2}-\d{2}$/.test(data.date) || Number.isNaN(Date.parse(data.date))) {
      throw new Error("Informe uma categoria, uma data e um responsável válidos.");
    }
    if (!data.participants.length || new Set(data.participants).size !== data.participants.length) {
      throw new Error("Selecione os moradores que vão dividir a despesa.");
    }
    if (data.splitMode === "custom") {
      const total = data.participants.reduce((sum, resident) => sum + (data.customShares?.[resident] ?? -1), 0);
      const hasInvalidAmount = data.participants.some((resident) => !Number.isSafeInteger(data.customShares?.[resident]) || (data.customShares?.[resident] ?? 0) < 0);
      if (hasInvalidAmount || total !== data.cents) {
        throw new Error("Os valores individuais devem somar exatamente o valor total da despesa.");
      }
    }
  }

  get snapshot() {
    return this.data;
  }

  get history() {
    return this.data.history ?? [{
      id: `created-${this.data.id}`,
      actor: this.data.payer,
      action: "Lançou esta despesa.",
      createdAt: `${this.data.date}T12:00:00.000Z`,
    }];
  }

  private withHistory(actor: ResidentId, action: string) {
    return [...this.history, { id: crypto.randomUUID(), actor, action, createdAt: new Date().toISOString() }];
  }

  share(resident: ResidentId) {
    const index = this.data.participants.indexOf(resident);
    if (index < 0) return 0;
    if (this.data.splitMode === "custom") return this.data.customShares?.[resident] ?? 0;
    const count = this.data.participants.length;
    return Math.floor(this.data.cents / count) + Number(index < this.data.cents % count);
  }

  pending(resident: ResidentId) {
    return this.data.payer === resident || this.data.paid.includes(resident) ? 0 : this.share(resident);
  }

  isPaymentConfirmed(resident: ResidentId) {
    if (!this.data.paid.includes(resident)) return false;
    return this.data.confirmed?.includes(resident) ?? true;
  }

  markPaid(resident: ResidentId) {
    if (!this.pending(resident)) throw new Error("Não há valor pendente.");
    if (this.data.dispute && this.data.dispute.status !== "resolved") {
      throw new Error("Resolva a contestação antes de registrar o pagamento.");
    }
    return new Expense({ ...this.data, paid: [...this.data.paid, resident], confirmed: this.data.confirmed ?? [], history: this.withHistory(resident, "Informou que realizou o pagamento da sua parte.") });
  }

  confirmPayment(confirmer: ResidentId, resident: ResidentId) {
    if (confirmer !== this.data.payer || !this.data.paid.includes(resident) || this.isPaymentConfirmed(resident)) {
      throw new Error("Este pagamento não pode ser confirmado agora.");
    }
    return new Expense({ ...this.data, confirmed: [...(this.data.confirmed ?? []), resident], history: this.withHistory(confirmer, `Confirmou o recebimento do pagamento de ${residents.find((item) => item.id === resident)?.name ?? resident}.`) });
  }

  remove(resident: ResidentId) {
    if (this.data.deleted) throw new Error("Esta despesa já foi excluída.");
    return new Expense({ ...this.data, deleted: { actor: resident, createdAt: new Date().toISOString() }, history: this.withHistory(resident, "Excluiu esta despesa do grupo.") });
  }

  restore(resident: ResidentId) {
    if (!this.data.deleted) throw new Error("Esta despesa não está no registro de exclusões.");
    const { deleted: _deleted, ...restored } = this.data;
    return new Expense({ ...restored, history: this.withHistory(resident, "Restaurou esta despesa no grupo.") });
  }

  edit(resident: ResidentId, changes: Pick<ExpenseData, "title" | "cents" | "category" | "date" | "payer">) {
    const updated = { ...this.data, ...changes };
    new Expense(updated);
    return new Expense({ ...updated, history: this.withHistory(resident, "Editou os dados da despesa.") });
  }

  contest(resident: ResidentId, reason: string, description: string, evidence?: Pick<Dispute, "evidence" | "evidenceName">) {
    if (!this.pending(resident) || this.data.dispute) throw new Error("Esta despesa não pode ser contestada.");
    if (!reason.trim() || !description.trim()) throw new Error("Preencha o motivo e descreva o problema.");
    return new Expense({ ...this.data, dispute: { reason, description: description.trim(), status: "open", author: resident, ...evidence }, history: this.withHistory(resident, `Contestou a despesa: ${reason}.`) });
  }

  review(resident: ResidentId, reply: string, cents: number) {
    const dispute = this.data.dispute;
    if (resident !== this.data.payer || dispute?.status !== "open" || !reply.trim()) {
      throw new Error("O responsável pela despesa deve informar a resposta.");
    }
    if (this.data.paid.length && cents !== this.data.cents) {
      throw new Error("O valor total não pode mudar depois que uma parte foi paga.");
    }
    const changedValue = cents !== this.data.cents ? " e revisou o valor" : "";
    return new Expense({ ...this.data, cents, dispute: { ...dispute, reply: reply.trim(), status: "reviewed" }, history: this.withHistory(resident, `Respondeu à contestação${changedValue}.`) });
  }

  requestFurtherReview(resident: ResidentId, comment: string) {
    const dispute = this.data.dispute;
    if (dispute?.author !== resident || dispute.status !== "reviewed" || !comment.trim()) {
      throw new Error("Escreva o que ainda precisa ser revisto.");
    }
    return new Expense({
      ...this.data,
      dispute: { ...dispute, status: "open", followUps: [...(dispute.followUps ?? []), comment.trim()] },
      history: this.withHistory(resident, "Enviou um comentário adicional na contestação."),
    });
  }

  resolve(resident: ResidentId) {
    const dispute = this.data.dispute;
    if (dispute?.author !== resident || dispute.status !== "reviewed") throw new Error("Aguarde a resposta do responsável.");
    return new Expense({ ...this.data, dispute: { ...dispute, status: "resolved" }, history: this.withHistory(resident, "Marcou a contestação como resolvida.") });
  }
}

export class Household {
  constructor(private readonly expenses: Expense[]) {}

  balance(resident: ResidentId) {
    return this.expenses.reduce((balance, expense) => {
      if (expense.snapshot.payer !== resident) {
        return { ...balance, owes: balance.owes + expense.pending(resident) };
      }
      const incoming = expense.snapshot.participants.reduce((sum, id) => sum + expense.pending(id), 0);
      return { ...balance, receives: balance.receives + incoming };
    }, { owes: 0, receives: 0 });
  }
}

export function parseCents(value: string) {
  const normalized = value.trim().replace(",", ".");
  if (!/^\d+(\.\d{1,2})?$/.test(normalized)) return 0;
  const [whole, fraction = ""] = normalized.split(".");
  return Number(whole) * 100 + Number(fraction.padEnd(2, "0"));
}
