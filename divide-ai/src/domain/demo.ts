import type { ExpenseData } from "./expense";

export const demoDataVersion = "two-years-v1";

const residents = ["voce", "ana", "leo"] as const;

function dateForMonth(index: number) {
  return new Date(Date.UTC(2024, 11 + index, 5)).toISOString().slice(0, 10);
}

const historicalExpenses: ExpenseData[] = Array.from({ length: 23 }, (_, index) => {
  const date = dateForMonth(index);
  const month = String(index + 1).padStart(2, "0");
  const electricity = 15200 + (index % 6) * 830;

  return [
    { id: `rent-${month}`, title: "Aluguel", cents: 180000, category: "Moradia", date, payer: "ana", participants: [...residents], paid: [...residents] },
    { id: `energy-${month}`, title: "Conta de energia", cents: electricity, category: "Contas", date, payer: "leo", participants: [...residents], paid: [...residents] },
    { id: `market-${month}`, title: "Compras da semana", cents: 21000 + (index % 5) * 1640, category: "Mercado", date, payer: "voce", participants: [...residents], paid: [...residents] },
  ] satisfies ExpenseData[];
}).flat();

const currentExpenses: ExpenseData[] = [
  { id: "rent-current", title: "Aluguel de novembro", cents: 180000, category: "Moradia", date: "2026-11-10", payer: "ana", participants: [...residents], paid: [] },
  { id: "groceries-current", title: "Compras da semana", cents: 23460, category: "Mercado", date: "2026-11-08", payer: "voce", participants: [...residents], paid: [] },
  { id: "internet-current", title: "Internet de novembro", cents: 11990, category: "Contas", date: "2026-11-05", payer: "leo", participants: [...residents], paid: [] },
  { id: "power-current", title: "Conta de energia", cents: 16830, category: "Contas", date: "2026-11-03", payer: "ana", participants: [...residents], paid: ["voce", "leo"] },
];

export const demoExpenses: ExpenseData[] = [...currentExpenses, ...historicalExpenses].map((expense) => ({ ...expense, groupId: "demo-home" }));
