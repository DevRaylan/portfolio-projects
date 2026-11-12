"use client";

import { useEffect, useRef, useState } from "react";
import type { FormEvent, ReactNode } from "react";
import { ArrowDownLeft, ArrowUpRight, Bell, Calculator, CalendarClock, ChevronRight, CircleUserRound, Copy, Download, History, House, LayoutGrid, MessageCircle, Paperclip, Plus, ReceiptText, Repeat2, RotateCcw, Search, Send, ShoppingBasket, TrendingUp, Users, Wallet, X, Zap } from "lucide-react";
import { categories, Expense, Household, parseCents, residents } from "../domain/expense";
import type { Category, ExpenseData, ResidentId } from "../domain/expense";
import { demoDataVersion, demoExpenses } from "../domain/demo";
import { demoUsers, demoUsersVersion } from "../domain/users";
import type { AppUser } from "../domain/users";

const money = (cents: number) => new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(cents / 100);
const dateLabel = (date: string) => new Intl.DateTimeFormat("pt-BR", { dateStyle: "medium", timeZone: "UTC" }).format(new Date(date));
const monthLabel = (month: string) => new Intl.DateTimeFormat("pt-BR", { month: "long", year: "numeric", timeZone: "UTC" }).format(new Date(`${month}-01T12:00:00.000Z`));
const eventDateLabel = (date: string) => new Intl.DateTimeFormat("pt-BR", { dateStyle: "short", timeStyle: "short" }).format(new Date(date));
const person = (id: ResidentId) => residents.find((item) => item.id === id)?.name;
const icons = { Moradia: House, Mercado: ShoppingBasket, Contas: Zap, Outros: ReceiptText };

function pixDemoCode(expense: Expense) {
  const value = (expense.pending("voce") / 100).toFixed(2);
  return `PIX-DEMO|recebedor=${person(expense.snapshot.payer) ?? "Morador"}|valor=${value}|referencia=${expense.snapshot.id}`;
}

async function readProof(file: FormDataEntryValue | null) {
  if (!(file instanceof File) || !file.size) return undefined;
  if (!['image/png', 'image/jpeg', 'image/webp', 'application/pdf'].includes(file.type) || file.size > 1024 * 1024) {
    throw new Error("Selecione um PNG, JPG, WebP ou PDF de até 1 MB.");
  }
  const evidence = await new Promise<string>((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(String(reader.result));
    reader.onerror = () => reject(new Error("Não foi possível ler a prova anexada."));
    reader.readAsDataURL(file);
  });
  return { evidence, evidenceName: file.name };
}

function nextMonth(date: string) {
  const [year, month, day] = date.split("-").map(Number);
  const next = new Date(Date.UTC(year, month, 1));
  const finalDay = new Date(Date.UTC(next.getUTCFullYear(), next.getUTCMonth() + 1, 0)).getUTCDate();
  next.setUTCDate(Math.min(day, finalDay));
  return next.toISOString().slice(0, 10);
}

function previousMonth(month: string) {
  const [year, value] = month.split("-").map(Number);
  return new Date(Date.UTC(year, value - 2, 1)).toISOString().slice(0, 7);
}

function localToday() {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0");
  const day = String(now.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
}

type HouseholdGroup = {
  id: string;
  name: string;
  residents: string[];
};

type Screen = "overview" | "groups" | "expenses" | "history";
type ReminderTiming = "due-date" | "one-day" | "three-days";
type ExpenseSort = "recent" | "oldest" | "highest" | "lowest" | "pending";
type HistoryFilter = "all" | "launches" | "payments" | "changes";
type HistoryRange = "30days" | "90days" | "year" | "all";

const reminderTimingLabels: Record<ReminderTiming, string> = {
  "due-date": "No dia do vencimento",
  "one-day": "1 dia antes",
  "three-days": "3 dias antes",
};

const demoGroups: HouseholdGroup[] = [
  { id: "demo-home", name: "Minha república", residents: ["Você", "Ana", "Léo"] },
];

function Modal({ title, close, children }: { title: string; close: () => void; children: ReactNode }) {
  const ref = useRef<HTMLDialogElement>(null);
  useEffect(() => { ref.current?.showModal(); }, []);
  return <dialog ref={ref} onCancel={(event) => { event.preventDefault(); close(); }} aria-labelledby="dialog-title">
    <div className="modal-heading"><h2 id="dialog-title">{title}</h2><button className="icon-button" onClick={close} aria-label="Fechar"><X size={20}/></button></div>
    {children}
  </dialog>;
}

export default function Dashboard({ account }: { account?: { id: string; name: string; email: string } }) {
  const storageKey = account ? `divide-ai.expenses.v1.google.${account.id}` : "divide-ai.expenses.v1";
  const [data, setData] = useState<ExpenseData[]>(demoExpenses);
  const [ready, setReady] = useState(false);
  const [filter, setFilter] = useState("Todas");
  const [period, setPeriod] = useState("latest");
  const [search, setSearch] = useState("");
  const [expenseSort, setExpenseSort] = useState<ExpenseSort>("recent");
  const [historyFilter, setHistoryFilter] = useState<HistoryFilter>("all");
  const [historyRange, setHistoryRange] = useState<HistoryRange>("year");
  const [screen, setScreen] = useState<Screen>("overview");
  const [modal, setModal] = useState<string | null>(null);
  const [error, setError] = useState("");
  const [notice, setNotice] = useState("");
  const [contesting, setContesting] = useState(false);
  const [editing, setEditing] = useState(false);
  const [deleting, setDeleting] = useState(false);
  const [copiedPix, setCopiedPix] = useState(false);
  const [copiedInvite, setCopiedInvite] = useState(false);
  const [copiedReminder, setCopiedReminder] = useState<ResidentId | null>(null);
  const [remindersEnabled, setRemindersEnabled] = useState(false);
  const [reminderSettingsOpen, setReminderSettingsOpen] = useState(false);
  const [reminderTiming, setReminderTiming] = useState<ReminderTiming>("one-day");
  const [creating, setCreating] = useState(false);
  const [groups, setGroups] = useState<HouseholdGroup[]>(demoGroups);
  const [activeGroupId, setActiveGroupId] = useState(demoGroups[0].id);
  const [groupPanelOpen, setGroupPanelOpen] = useState(false);
  const [groupModalOpen, setGroupModalOpen] = useState(false);
  const [splitMode, setSplitMode] = useState<"equal" | "custom">("equal");
  const [selectedResidents, setSelectedResidents] = useState<ResidentId[]>(["voce", "ana", "leo"]);
  const [users, setUsers] = useState<AppUser[]>([]);
  const [memberToAdd, setMemberToAdd] = useState("");
  const [quickTemplate, setQuickTemplate] = useState<ExpenseData | null>(null);
  const creatingRef = useRef(false);
  const groupsKey = `${storageKey}.groups`;
  const activeGroupKey = `${storageKey}.active-group`;
  const demoSeedKey = `${storageKey}.demo-data-version`;
  const usersKey = "divide-ai.users.v1";
  const usersSeedKey = "divide-ai.users-version";
  const remindersKey = `${storageKey}.reminders`;

  useEffect(() => {
    try {
      const saved = localStorage.getItem(storageKey);
      const savedExpenses = saved ? JSON.parse(saved).map((item: ExpenseData) => ({ ...new Expense(item).snapshot, groupId: item.groupId ?? "demo-home" })) : [];
      const savedVersion = localStorage.getItem(demoSeedKey);
      const existingIds = new Set(savedExpenses.map((item: ExpenseData) => item.id));
      const missingDemoExpenses = savedVersion === demoDataVersion ? [] : demoExpenses.filter((item) => !existingIds.has(item.id));
      const nextExpenses = [...savedExpenses, ...missingDemoExpenses];
      localStorage.setItem(storageKey, JSON.stringify(nextExpenses));
      setData(nextExpenses);
      localStorage.setItem(demoSeedKey, demoDataVersion);
      const savedUsers = JSON.parse(localStorage.getItem(usersKey) ?? "[]") as typeof demoUsers;
      const knownUserIds = new Set(savedUsers.map((user) => user.id));
      const nextUsers = savedUsers.length && localStorage.getItem(usersSeedKey) === demoUsersVersion
        ? savedUsers
        : [...savedUsers, ...demoUsers.filter((user) => !knownUserIds.has(user.id))];
      localStorage.setItem(usersKey, JSON.stringify(nextUsers));
      localStorage.setItem(usersSeedKey, demoUsersVersion);
      setUsers(nextUsers);
      const savedReminders = localStorage.getItem(remindersKey);
      if (savedReminders) {
        const reminderConfig = JSON.parse(savedReminders) as { enabled?: boolean; timing?: ReminderTiming };
        setRemindersEnabled(Boolean(reminderConfig.enabled));
        if (reminderConfig.timing && reminderConfig.timing in reminderTimingLabels) setReminderTiming(reminderConfig.timing);
      }
      const savedGroups = localStorage.getItem(groupsKey);
      const savedActiveGroup = localStorage.getItem(activeGroupKey);
      if (savedGroups) {
        const parsed = JSON.parse(savedGroups) as HouseholdGroup[];
        if (parsed.length) {
          setGroups(parsed);
          setActiveGroupId(parsed.some((group) => group.id === savedActiveGroup) ? savedActiveGroup! : parsed[0].id);
        }
      }
    } catch {
      setNotice("Não foi possível recuperar os dados locais. Os exemplos estão em exibição.");
    }
    setReady(true);
  }, [storageKey, groupsKey, activeGroupKey, demoSeedKey, usersKey, usersSeedKey, remindersKey]);

  useEffect(() => {
    const syncScreen = () => {
      const next = window.location.hash.replace("#", "");
      setScreen(next === "grupos" ? "groups" : next === "despesas" ? "expenses" : next === "historico" ? "history" : "overview");
    };
    syncScreen();
    window.addEventListener("hashchange", syncScreen);
    return () => window.removeEventListener("hashchange", syncScreen);
  }, []);

  const expenses = data.map((item) => new Expense(item));
  const groupExpenses = expenses.filter((item) => item.snapshot.groupId === activeGroupId && !item.snapshot.deleted);
  const deletedExpenses = expenses.filter((item) => item.snapshot.groupId === activeGroupId && item.snapshot.deleted).sort((first, second) => (second.snapshot.deleted?.createdAt ?? "").localeCompare(first.snapshot.deleted?.createdAt ?? ""));
  const availableMonths = [...new Set(groupExpenses.map((item) => item.snapshot.date.slice(0, 7)))].sort().reverse();
  const activePeriod = period === "latest" ? availableMonths[0] : period;
  const periodExpenses = activePeriod === "all" || !activePeriod ? groupExpenses : groupExpenses.filter((item) => item.snapshot.date.startsWith(activePeriod));
  const balance = new Household(groupExpenses).balance("voce");
  const selected = groupExpenses.find((item) => item.snapshot.id === modal);
  const awaitingConfirmation = selected && selected.snapshot.payer !== "voce" && selected.snapshot.paid.includes("voce") && !selected.isPaymentConfirmed("voce");
  const residentsToRemind = selected?.snapshot.payer === "voce"
    ? selected.snapshot.participants.filter((resident) => resident !== "voce" && !selected.snapshot.paid.includes(resident))
    : [];
  const equalSplitHasRemainder = Boolean(selected && selected.snapshot.splitMode !== "custom" && selected.snapshot.cents % (selected.snapshot.participants.length || 1) !== 0);
  const shown = periodExpenses.filter((item) => {
    if (filter !== "Todas" && item.snapshot.category !== filter) return false;
    const query = search.trim().toLocaleLowerCase("pt-BR");
    if (!query) return true;
    const payerName = person(item.snapshot.payer) ?? item.snapshot.payer;
    return [item.snapshot.title, item.snapshot.category, payerName].some((value) => value.toLocaleLowerCase("pt-BR").includes(query));
  }).sort((first, second) => {
    if (expenseSort === "oldest") return first.snapshot.date.localeCompare(second.snapshot.date);
    if (expenseSort === "highest") return second.snapshot.cents - first.snapshot.cents;
    if (expenseSort === "lowest") return first.snapshot.cents - second.snapshot.cents;
    if (expenseSort === "pending") {
      const firstPending = Number(first.snapshot.participants.some((resident) => first.pending(resident) > 0));
      const secondPending = Number(second.snapshot.participants.some((resident) => second.pending(resident) > 0));
      return secondPending - firstPending || second.snapshot.date.localeCompare(first.snapshot.date);
    }
    return second.snapshot.date.localeCompare(first.snapshot.date);
  });
  const periodTotal = periodExpenses.reduce((sum, item) => sum + item.snapshot.cents, 0);
  const categoryTotals = categories.map((category) => ({ category, cents: periodExpenses.filter((item) => item.snapshot.category === category).reduce((sum, item) => sum + item.snapshot.cents, 0) })).filter((item) => item.cents > 0);
  const paymentReminders = groupExpenses.filter((item) => item.pending("voce") > 0).sort((first, second) => first.snapshot.date.localeCompare(second.snapshot.date));
  const latestMonth = availableMonths[0];
  const monthExpenses = latestMonth ? groupExpenses.filter((item) => item.snapshot.date.startsWith(latestMonth)) : [];
  const previousExpenses = latestMonth ? groupExpenses.filter((item) => item.snapshot.date.startsWith(previousMonth(latestMonth))) : [];
  const monthTotal = monthExpenses.reduce((sum, item) => sum + item.snapshot.cents, 0);
  const previousTotal = previousExpenses.reduce((sum, item) => sum + item.snapshot.cents, 0);
  const monthChange = previousTotal ? Math.round(((monthTotal - previousTotal) / previousTotal) * 100) : null;
  const monthCategories = categories.map((category) => ({ category, cents: monthExpenses.filter((item) => item.snapshot.category === category).reduce((sum, item) => sum + item.snapshot.cents, 0) })).filter((item) => item.cents > 0);
  const largestCategory = Math.max(...monthCategories.map((item) => item.cents), 1);
  const priorityPayment = [...paymentReminders].sort((first, second) => second.pending("voce") - first.pending("voce"))[0];
  const recurringExpenses = groupExpenses.filter((item) => item.snapshot.recurrence).sort((first, second) => first.snapshot.date.localeCompare(second.snapshot.date));
  const groupHistory = expenses.filter((expense) => expense.snapshot.groupId === activeGroupId).flatMap((expense) => expense.history.map((event) => ({ expense, event }))).sort((first, second) => second.event.createdAt.localeCompare(first.event.createdAt));
  const eventKind = (action: string): Exclude<HistoryFilter, "all"> => {
    const text = action.toLocaleLowerCase("pt-BR");
    if (text.includes("pagamento") || text.includes("pagou") || text.includes("confirmou")) return "payments";
    if (text.includes("alterou") || text.includes("excluiu") || text.includes("restaurou") || text.includes("contest")) return "changes";
    return "launches";
  };
  const latestHistoryDate = groupHistory[0]?.event.createdAt ? new Date(groupHistory[0].event.createdAt) : undefined;
  const historyRangeDays: Record<Exclude<HistoryRange, "all">, number> = { "30days": 30, "90days": 90, year: 365 };
  const historyRangeStart = latestHistoryDate && historyRange !== "all" ? new Date(latestHistoryDate) : undefined;
  if (historyRangeStart && historyRange !== "all") historyRangeStart.setDate(historyRangeStart.getDate() - historyRangeDays[historyRange]);
  const filteredHistory = groupHistory.filter(({ event }) => (!historyRangeStart || new Date(event.createdAt) >= historyRangeStart) && (historyFilter === "all" || eventKind(event.action) === historyFilter));
  const historyMonths = filteredHistory.reduce<{ month: string; items: typeof filteredHistory }[]>((months, item) => {
    const month = item.event.createdAt.slice(0, 7);
    const last = months.at(-1);
    if (last?.month === month) last.items.push(item);
    else months.push({ month, items: [item] });
    return months;
  }, []);
  const recentActivity = groupHistory.slice(0, 4);
  const quickLaunches = [
    { label: "Aluguel", match: "aluguel" },
    { label: "Mercado", match: "mercado", category: "Mercado" as Category },
    { label: "Internet", match: "internet" },
    { label: "Energia", match: "energia" },
  ].map((item) => ({ ...item, expense: groupExpenses.filter((expense) => expense.snapshot.title.toLocaleLowerCase("pt-BR").includes(item.match) || expense.snapshot.category === item.category).sort((first, second) => second.snapshot.date.localeCompare(first.snapshot.date))[0] })).filter((item): item is { label: string; match: string; category?: Category; expense: Expense } => Boolean(item.expense));
  const activeGroup = groups.find((group) => group.id === activeGroupId) ?? groups[0];
  const searchQuery = search.trim().toLocaleLowerCase("pt-BR");
  const searchExpenseResults = searchQuery ? expenses.filter((expense) => !expense.snapshot.deleted && [expense.snapshot.title, expense.snapshot.category, person(expense.snapshot.payer) ?? expense.snapshot.payer, groups.find((group) => group.id === expense.snapshot.groupId)?.name ?? ""].some((value) => value.toLocaleLowerCase("pt-BR").includes(searchQuery))).slice(0, 5) : [];
  const searchGroupResults = searchQuery ? groups.filter((group) => group.name.toLocaleLowerCase("pt-BR").includes(searchQuery)).slice(0, 3) : [];
  const searchResidentResults = searchQuery ? groups.flatMap((group) => group.residents.map((resident) => ({ group, resident }))).filter(({ resident }) => resident.toLocaleLowerCase("pt-BR").includes(searchQuery)).slice(0, 4) : [];
  const onboardingSteps = [
    { id: "group", label: "Crie um grupo de moradia", helper: "Defina a casa, apartamento ou república.", done: Boolean(activeGroup) },
    { id: "residents", label: "Adicione moradores", helper: "Inclua pelo menos mais uma pessoa para dividir as contas.", done: (activeGroup?.residents.length ?? 0) > 1 },
    { id: "expense", label: "Lance a primeira despesa", helper: "Registre uma conta para começar a acompanhar os acertos.", done: groupExpenses.length > 0 },
  ] as const;
  const completedOnboardingSteps = onboardingSteps.filter((step) => step.done).length;
  const open = (id: string | null) => { setError(""); setContesting(false); setEditing(false); setDeleting(false); setCopiedPix(false); setCopiedReminder(null); if (id !== "new") setQuickTemplate(null); setModal(id); };

  function navigate(next: Screen) {
    setScreen(next);
    window.location.hash = next === "overview" ? "visao-geral" : next === "groups" ? "grupos" : next === "history" ? "historico" : "despesas";
  }

  function openNewExpense() {
    setQuickTemplate(null);
    setSplitMode("equal");
    setSelectedResidents(["voce", "ana", "leo"]);
    open("new");
  }

  function openQuickExpense(expense: Expense) {
    setQuickTemplate(expense.snapshot);
    setSplitMode(expense.snapshot.splitMode ?? "equal");
    setSelectedResidents(expense.snapshot.participants);
    open("new");
  }

  function save(next: ExpenseData[], message: string) {
    localStorage.setItem(storageKey, JSON.stringify(next));
    setData(next);
    setNotice(message);
    setError("");
  }

  function update(action: () => Expense, message: string) {
    try {
      const next = action().snapshot;
      save(data.map((item) => item.id === next.id ? next : item), message);
      setContesting(false);
      return true;
    } catch (failure) {
      setError(failure instanceof Error ? failure.message : "Não foi possível salvar.");
      return false;
    }
  }

  function deleteExpense(expense: Expense) {
    try {
      const next = expense.remove("voce").snapshot;
      save(data.map((item) => item.id === next.id ? next : item), "Despesa excluída e registrada no histórico.");
    } catch (failure) {
      setError(failure instanceof Error ? failure.message : "Não foi possível excluir a despesa.");
    }
    open(null);
  }

  function restoreExpense(expense: Expense) {
    try {
      const next = expense.restore("voce").snapshot;
      save(data.map((item) => item.id === next.id ? next : item), "Despesa restaurada e adicionada novamente à lista do grupo.");
    } catch (failure) {
      setError(failure instanceof Error ? failure.message : "Não foi possível restaurar a despesa.");
    }
  }

  async function copyPix(expense: Expense) {
    try {
      await navigator.clipboard.writeText(pixDemoCode(expense));
      setCopiedPix(true);
      setNotice("Código Pix de demonstração copiado.");
      setError("");
    } catch {
      setError("Não foi possível copiar o código Pix neste navegador.");
    }
  }

  function whatsAppUrl(expense: Expense) {
    const message = [
      `Olá! Sobre a despesa *${expense.snapshot.title}* no grupo *${activeGroup?.name ?? "Divide Aí"}*:`,
      `o valor pendente é *${money(expense.pending("voce"))}* para ${person(expense.snapshot.payer)}.`,
      "",
      `Pix (demonstração): ${pixDemoCode(expense)}`,
    ].join("\n");
    return `https://wa.me/?text=${encodeURIComponent(message)}`;
  }

  function reminderMessage(expense: Expense, resident: ResidentId) {
    return [
      `Olá, ${person(resident)}!`,
      `Passando para lembrar da despesa *${expense.snapshot.title}* no grupo *${activeGroup?.name ?? "Divide Aí"}*.`,
      `Sua parte é *${money(expense.share(resident))}*.`,
      "Quando fizer o Pix, marque o pagamento no Divide Aí para manter tudo organizado.",
    ].join("\n");
  }

  function reminderWhatsAppUrl(expense: Expense, resident: ResidentId) {
    return `https://wa.me/?text=${encodeURIComponent(reminderMessage(expense, resident))}`;
  }

  async function copyReminder(expense: Expense, resident: ResidentId) {
    try {
      await navigator.clipboard.writeText(reminderMessage(expense, resident));
      setCopiedReminder(resident);
      setNotice(`Lembrete para ${person(resident)} copiado.`);
      setError("");
    } catch {
      setError("Não foi possível copiar o lembrete neste navegador.");
    }
  }

  function addNextMonthlyExpense(expense: Expense) {
    const date = nextMonth(expense.snapshot.date);
    const alreadyExists = data.some((item) => item.groupId === expense.snapshot.groupId && item.title === expense.snapshot.title && item.date === date);
    if (alreadyExists) {
      setError("A próxima mensalidade desta despesa já foi lançada.");
      return;
    }
    const id = crypto.randomUUID();
    const next = new Expense({
      ...expense.snapshot,
      id,
      date,
      paid: [],
      receipt: undefined,
      dispute: undefined,
      history: [{ id: crypto.randomUUID(), actor: "voce", action: "Criou a próxima mensalidade desta despesa recorrente.", createdAt: new Date().toISOString() }],
    });
    save([next.snapshot, ...data], "Próxima mensalidade adicionada.");
    open(id);
  }

  function exportReport() {
    const escape = (value: string | number) => `"${String(value).replaceAll('"', '""')}"`;
    const rows = periodExpenses.map((expense) => [
      expense.snapshot.date,
      expense.snapshot.title,
      expense.snapshot.category,
      person(expense.snapshot.payer) ?? expense.snapshot.payer,
      (expense.snapshot.cents / 100).toFixed(2).replace('.', ','),
      (expense.share("voce") / 100).toFixed(2).replace('.', ','),
      expense.snapshot.paid.includes("voce") || expense.snapshot.payer === "voce" ? "Pago" : "Pendente",
    ].map(escape).join(';'));
    const csv = `\uFEFF${["Data", "Despesa", "Categoria", "Responsável", "Valor total", "Sua parte", "Situação"].map(escape).join(';')}\n${rows.join('\n')}`;
    const file = new Blob([csv], { type: 'text/csv;charset=utf-8' });
    const url = URL.createObjectURL(file);
    const link = document.createElement('a');
    link.href = url;
    link.download = `divide-ai-${activePeriod === "all" || !activePeriod ? "historico" : activePeriod}.csv`;
    link.click();
    URL.revokeObjectURL(url);
    setNotice("Relatório CSV baixado.");
  }

  async function enableReminders() {
    if (!("Notification" in window)) {
      setError("Este navegador não oferece notificações.");
      return;
    }
    const permission = await Notification.requestPermission();
    if (permission !== "granted") {
      setError("Você precisa permitir notificações para receber lembretes.");
      return;
    }
    setRemindersEnabled(true);
    localStorage.setItem(remindersKey, JSON.stringify({ enabled: true, timing: reminderTiming }));
    paymentReminders.slice(0, 3).forEach((expense) => {
      new Notification("Divide Aí · pagamento pendente", {
        body: `${expense.snapshot.title}: você deve ${money(expense.pending("voce"))} para ${person(expense.snapshot.payer)}.`,
      });
    });
    setNotice(paymentReminders.length ? "Lembretes ativados e avisos atuais enviados." : "Lembretes ativados. Não há pagamentos pendentes.");
    setError("");
  }

  function saveReminderSettings(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    localStorage.setItem(remindersKey, JSON.stringify({ enabled: remindersEnabled, timing: reminderTiming }));
    setReminderSettingsOpen(false);
    setNotice(remindersEnabled ? `Avisos configurados para ${reminderTimingLabels[reminderTiming].toLowerCase()}.` : "Preferência de aviso salva. Ative as notificações quando quiser.");
  }

  function saveGroups(next: HouseholdGroup[], message: string) {
    localStorage.setItem(groupsKey, JSON.stringify(next));
    setGroups(next);
    setNotice(message);
  }

  function selectGroup(groupId: string) {
    localStorage.setItem(activeGroupKey, groupId);
    setActiveGroupId(groupId);
    setPeriod("latest");
    setCopiedInvite(false);
    setGroupPanelOpen(false);
    setNotice("Grupo selecionado.");
  }

  function inviteCode(group: HouseholdGroup) {
    return `DIVIDE-${group.id.replaceAll("-", "").slice(0, 7).toUpperCase()}`;
  }

  async function copyInvite(group: HouseholdGroup) {
    const url = `${window.location.origin}${window.location.pathname}?invite=${encodeURIComponent(group.id)}`;
    const text = `Entre no grupo "${group.name}" no Divide Aí. Código: ${inviteCode(group)}\n${url}`;
    try {
      await navigator.clipboard.writeText(text);
      setCopiedInvite(true);
      setNotice("Convite copiado.");
      setError("");
    } catch {
      setError("Não foi possível copiar o convite neste navegador.");
    }
  }

  function createGroup(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const form = new FormData(event.currentTarget);
    const name = String(form.get("groupName")).trim();
    const residentsText = String(form.get("residents")).trim();
    const residentNames = residentsText.split(",").map((resident) => resident.trim()).filter(Boolean);
    if (!name || residentNames.length === 0) {
      setError("Informe o nome do grupo e pelo menos um morador.");
      return;
    }
    const group = { id: crypto.randomUUID(), name, residents: [...new Set(residentNames)] };
    const next = [...groups, group];
    saveGroups(next, "Grupo criado.");
    selectGroup(group.id);
    setGroupModalOpen(false);
    setError("");
  }

  function addResidentToGroup(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const user = users.find((item) => item.id === memberToAdd);
    if (!user || !activeGroup) {
      setError("Selecione um usuário para adicionar.");
      return;
    }
    if (activeGroup.residents.includes(user.name)) {
      setError("Este usuário já faz parte do grupo.");
      return;
    }
    const next = groups.map((group) => group.id === activeGroup.id ? { ...group, residents: [...group.residents, user.name] } : group);
    saveGroups(next, `${user.name} foi adicionado ao grupo.`);
    setMemberToAdd("");
    setError("");
  }

  async function create(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    if (creatingRef.current) return;
    const form = new FormData(event.currentTarget);
    creatingRef.current = true;
    setCreating(true);
    try {
      const file = form.get("receipt");
      let receipt: string | undefined;
      if (file instanceof File && file.size) {
        if (!["image/png", "image/jpeg", "image/webp", "application/pdf"].includes(file.type) || file.size > 1024 * 1024) {
          throw new Error("Selecione um PNG, JPG, WebP ou PDF de até 1 MB.");
        }
        receipt = await new Promise<string>((resolve, reject) => {
          const reader = new FileReader();
          reader.onload = () => resolve(String(reader.result));
          reader.onerror = () => reject(new Error("Não foi possível ler o comprovante."));
          reader.readAsDataURL(file);
        });
      }
      const expense = new Expense({
        id: crypto.randomUUID(), groupId: activeGroupId, title: String(form.get("title")).trim(), cents: parseCents(String(form.get("amount"))),
        category: form.get("category") as Category, date: String(form.get("date")), payer: form.get("payer") as ResidentId,
        participants: selectedResidents, splitMode,
        recurrence: form.get("recurring") === "on" ? { frequency: "monthly" } : undefined,
        customShares: splitMode === "custom" ? Object.fromEntries(selectedResidents.map((resident) => [resident, parseCents(String(form.get(`share-${resident}`)))])) : undefined,
        paid: [], receipt,
        history: [{ id: crypto.randomUUID(), actor: form.get("payer") as ResidentId, action: "Lançou esta despesa.", createdAt: new Date().toISOString() }],
      });
      save([expense.snapshot, ...data], "Despesa adicionada.");
      setFilter("Todas");
      setQuickTemplate(null);
      setSplitMode("equal");
      setSelectedResidents(["voce", "ana", "leo"]);
      open(null);
    } catch (failure) {
      setError(failure instanceof Error ? failure.message : "Não foi possível salvar.");
    } finally {
      creatingRef.current = false;
      setCreating(false);
    }
  }

  return <div className="shell">
    <aside className="sidebar">
      <div className="brand"><span className="brand-icon"><Wallet size={22}/></span>Divide Aí<span className="brand-dot">.</span></div>
      <nav aria-label="Navegação"><button className={`nav-button ${screen === "overview" ? "active" : ""}`} onClick={() => navigate("overview")}><LayoutGrid size={18}/>Visão geral</button><button className={`nav-button ${screen === "groups" ? "active" : ""}`} onClick={() => navigate("groups")}><Users size={18}/>Grupos e moradores</button><button className={`nav-button ${screen === "expenses" ? "active" : ""}`} onClick={() => navigate("expenses")}><ReceiptText size={18}/>Despesas</button><button className={`nav-button ${screen === "history" ? "active" : ""}`} onClick={() => navigate("history")}><History size={18}/>Histórico</button></nav>
      <div className="sidebar-footer">
        <div className="profile"><span className="profile-avatar">{account ? account.name.slice(0, 1).toUpperCase() : <CircleUserRound size={19}/>}</span><div><strong>{account?.name ?? "Você"}</strong><small>{account?.email ?? "Demonstração local"}</small></div>{account && <form action="/api/auth/logout" method="post"><button className="icon-button" type="submit" title="Sair" aria-label="Sair"><X size={17}/></button></form>}</div>
      </div>
    </aside>

    <main id="visao-geral">
      <header className="topbar" aria-label="Pesquisa global">
        <div className="global-search"><Search size={19}/><input value={search} onChange={(event) => setSearch(event.target.value)} placeholder="Pesquisar despesas, grupos ou moradores" aria-label="Pesquisar no Divide Aí"/>{search && <button className="search-clear" onClick={() => setSearch("")} aria-label="Limpar pesquisa"><X size={16}/></button>}
          {searchQuery && <div className="search-results" role="status">{searchExpenseResults.length + searchGroupResults.length + searchResidentResults.length ? <>
            {searchExpenseResults.length > 0 && <section><span>Despesas</span>{searchExpenseResults.map((expense) => <button key={expense.snapshot.id} onClick={() => { setSearch(""); if (expense.snapshot.groupId !== activeGroupId) selectGroup(expense.snapshot.groupId ?? "demo-home"); navigate("expenses"); open(expense.snapshot.id); }}><ReceiptText size={16}/><div><strong>{expense.snapshot.title}</strong><small>{groups.find((group) => group.id === expense.snapshot.groupId)?.name ?? "Grupo"} · {money(expense.snapshot.cents)}</small></div><ChevronRight size={15}/></button>)}</section>}
            {searchGroupResults.length > 0 && <section><span>Grupos</span>{searchGroupResults.map((group) => <button key={group.id} onClick={() => { setSearch(""); selectGroup(group.id); navigate("groups"); }}><House size={16}/><div><strong>{group.name}</strong><small>{group.residents.length} moradores</small></div><ChevronRight size={15}/></button>)}</section>}
            {searchResidentResults.length > 0 && <section><span>Moradores</span>{searchResidentResults.map(({ group, resident }) => <button key={`${group.id}-${resident}`} onClick={() => { setSearch(""); selectGroup(group.id); navigate("groups"); }}><Users size={16}/><div><strong>{resident}</strong><small>{group.name}</small></div><ChevronRight size={15}/></button>)}</section>}
          </> : <p>Nenhum resultado encontrado.</p>}</div>}
        </div>
        <span className="topbar-context">{activeGroup?.name}</span>
      </header>
      {screen === "overview" && <section className="overview-page">
      <header className="top"><div><h1>Visão geral</h1><p>{account ? `Olá, ${account.name}.` : "Acompanhe os gastos e acertos da sua casa."}</p></div></header>
      <section className="summary" aria-label="Resumo financeiro">
        <article className="card balance"><span className="metric-icon"><Wallet size={20}/></span><p>Seu saldo líquido</p><strong>{money(balance.receives - balance.owes)}</strong><small>A receber menos a pagar</small></article>
        <article className="card"><span className="metric-icon outgoing"><ArrowUpRight size={20}/></span><p>Você deve</p><strong>{money(balance.owes)}</strong><small>Em despesas compartilhadas</small></article>
        <article className="card"><span className="metric-icon incoming"><ArrowDownLeft size={20}/></span><p>Você tem a receber</p><strong>{money(balance.receives)}</strong><small>Dos moradores do grupo</small></article>
      </section>

      <section className="attention-card" aria-labelledby="attention-title">
        <div><span className="eyebrow"><Bell size={15}/>O que precisa da sua atenção</span><h2 id="attention-title">{priorityPayment ? `${priorityPayment.snapshot.title} ainda está pendente` : "Tudo acertado por aqui"}</h2><p>{priorityPayment ? `Você deve ${money(priorityPayment.pending("voce"))} para ${person(priorityPayment.snapshot.payer)}.` : "Você não tem pagamentos pendentes neste grupo."}</p></div>
        <div className="attention-actions">{priorityPayment && <button className="primary" onClick={() => open(priorityPayment.snapshot.id)}>Resolver agora<ChevronRight size={17}/></button>}<button className="secondary" onClick={() => setReminderSettingsOpen(true)}><Bell size={17}/>{remindersEnabled ? "Configurar avisos" : "Ativar avisos"}</button></div>
      </section>

      <section className="overview-grid" aria-label="Resumo mensal e gastos por categoria">
        <article className="insight-card month-insight"><span className="metric-icon"><TrendingUp size={20}/></span><p>Gastos de {latestMonth ? monthLabel(latestMonth) : "este mês"}</p><strong>{money(monthTotal)}</strong><small>{monthChange === null ? "Primeiro mês com dados para comparação" : `${monthChange >= 0 ? "+" : ""}${monthChange}% em relação ao mês anterior`}</small></article>
        <article className="insight-card categories-insight"><div className="insight-heading"><div><h2>Por categoria</h2><p>Como o grupo gastou neste mês</p></div><button className="text-action" onClick={() => navigate("expenses")}>Ver despesas<ChevronRight size={15}/></button></div><div className="category-bars">{monthCategories.map((item) => <div key={item.category}><span>{item.category}</span><div className="bar-track"><i style={{ width: `${Math.max((item.cents / largestCategory) * 100, 8)}%` }}/></div><strong>{money(item.cents)}</strong></div>)}</div></article>
      </section>

      <section className="overview-grid detail-grid" aria-label="Próximas contas e atividade recente">
        <article className="insight-card compact-insight"><div className="insight-heading"><div><h2>Contas recorrentes</h2><p>Próximos lançamentos programados</p></div><CalendarClock size={20}/></div>{recurringExpenses.length ? <ul className="overview-list">{recurringExpenses.slice(0, 3).map((expense) => <li key={expense.snapshot.id}><span><strong>{expense.snapshot.title}</strong><small>Último lançamento: {dateLabel(expense.snapshot.date)}</small></span><strong>{money(expense.snapshot.cents)}</strong></li>)}</ul> : <p className="empty-state">Nenhuma conta recorrente cadastrada ainda.</p>}</article>
        <article className="insight-card compact-insight"><div className="insight-heading"><div><h2>Atividade recente</h2><p>Movimentações no grupo</p></div></div>{recentActivity.length ? <ul className="activity-list">{recentActivity.map(({ expense, event }) => <li key={event.id}><span className="activity-dot"/><span><strong>{expense.snapshot.title}</strong><small>{person(event.actor)} · {event.action}</small></span><time>{eventDateLabel(event.createdAt)}</time></li>)}</ul> : <p className="empty-state">Ainda não há atividades registradas.</p>}</article>
      </section>
      </section>}

      {screen === "groups" && <section className="groups-page" aria-labelledby="groups-title">
        <div className="section-head"><div><h1 id="groups-title">Grupos e moradores</h1><p>Organize as moradias, os participantes e os convites.</p></div><button className="primary" onClick={() => setGroupModalOpen(true)}><Plus size={18}/>Criar grupo</button></div>
        {activeGroup && <section className="active-group-hero" aria-label={`Grupo ativo: ${activeGroup.name}`}><div><span className="hero-kicker"><House size={15}/>Grupo ativo</span><h2>{activeGroup.name}</h2><p>{activeGroup.residents.length} {activeGroup.residents.length === 1 ? "morador" : "moradores"} organizando as despesas juntos.</p></div><div className="member-avatars" aria-label="Moradores do grupo">{activeGroup.residents.map((resident) => <span key={resident} title={resident}>{resident.slice(0, 1).toUpperCase()}</span>)}</div></section>}
        <section className={`setup-checklist ${completedOnboardingSteps === onboardingSteps.length ? "complete" : ""}`} aria-labelledby="setup-title"><div className="setup-heading"><div><span className="eyebrow">Primeiros passos</span><h2 id="setup-title">{completedOnboardingSteps === onboardingSteps.length ? "Seu grupo está pronto" : "Organize sua casa em poucos passos"}</h2><p>{completedOnboardingSteps === onboardingSteps.length ? "Você já pode acompanhar despesas, pagamentos e acertos normalmente." : "Conclua estas etapas para começar a dividir as despesas com clareza."}</p></div><strong>{completedOnboardingSteps}/{onboardingSteps.length}</strong></div><ol>{onboardingSteps.map((step) => <li key={step.id} className={step.done ? "done" : ""}><span className="setup-marker" aria-hidden>{step.done ? "✓" : ""}</span><span><strong>{step.label}</strong><small>{step.done ? "Concluído" : step.helper}</small></span>{!step.done && <button className="text-action" onClick={() => step.id === "group" ? setGroupModalOpen(true) : step.id === "residents" ? setGroupPanelOpen(true) : navigate("expenses")}>{step.id === "expense" ? "Lançar agora" : "Continuar"}<ChevronRight size={15}/></button>}</li>)}</ol></section>
        <div className="groups-grid">
          <section className="groups-directory" aria-labelledby="your-groups-title"><div className="panel-title"><div><h2 id="your-groups-title">Seus grupos</h2><p>{groups.length} {groups.length === 1 ? "grupo disponível" : "grupos disponíveis"}</p></div></div><div className="group-list">
            {groups.map((group) => <button className={`group-card ${group.id === activeGroupId ? "selected" : ""}`} key={group.id} onClick={() => selectGroup(group.id)}>
              <span className="group-icon"><House size={18}/></span><span><strong>{group.name}</strong><small>{group.residents.join(" · ")}</small></span>{group.id === activeGroupId && <span className="badge">Atual</span>}
            </button>)}
          </div></section>
          {activeGroup && <section className="members-panel" aria-labelledby="members-title"><div className="panel-title"><div><h2 id="members-title">Moradores</h2><p>Participantes de {activeGroup.name}</p></div></div><ul>{activeGroup.residents.map((resident) => <li key={resident}><span className="resident-avatar">{resident.slice(0, 1).toUpperCase()}</span><span><strong>{resident}</strong><small>{resident === "Você" ? "Você" : "Morador do grupo"}</small></span></li>)}</ul><form className="add-member" onSubmit={addResidentToGroup}>
            <label>Adicionar morador<select value={memberToAdd} onChange={(event) => setMemberToAdd(event.target.value)}><option value="">Selecione um usuário</option>{users.filter((user) => !activeGroup.residents.includes(user.name)).map((user) => <option key={user.id} value={user.id}>{user.name} · {user.email}</option>)}</select></label>
            <button className="secondary" type="submit" disabled={!memberToAdd}><Users size={17}/>Adicionar</button>
          </form></section>}
        </div>
        {activeGroup && <section className="invite-card" aria-labelledby="invite-title"><div><h3 id="invite-title">Convidar para {activeGroup.name}</h3><p>Compartilhe este convite com quem vai morar junto.</p></div><code>{inviteCode(activeGroup)}</code><button className="secondary" onClick={() => copyInvite(activeGroup)}><Copy size={17}/>{copiedInvite ? "Convite copiado" : "Copiar link e código"}</button><small>Convite de demonstração: a entrada real será ativada quando o banco de dados e o login estiverem conectados.</small></section>}
        {error && <p className="error" role="alert">{error}</p>}
      </section>}

      {screen === "history" && <section className="history-page" aria-labelledby="history-title">
        <div className="history-page-heading"><div><span className="eyebrow"><History size={15}/>Atividade financeira</span><h1 id="history-title">Histórico</h1><p>Consulte os lançamentos e acertos de {activeGroup?.name}.</p></div><button className="secondary" onClick={() => navigate("expenses")}><ReceiptText size={17}/>Ver despesas</button></div>
        <section className="history-toolbar" aria-label="Controles do histórico"><label>Período<select value={historyRange} onChange={(event) => setHistoryRange(event.target.value as HistoryRange)}><option value="30days">Últimos 30 dias</option><option value="90days">Últimos 90 dias</option><option value="year">Últimos 12 meses</option><option value="all">Todo o histórico</option></select></label><span className="history-toolbar-divider"/><div className="history-controls" aria-label="Filtrar por tipo">{([ ["all", "Tudo"], ["launches", "Lançamentos"], ["payments", "Pagamentos"], ["changes", "Alterações"] ] as const).map(([value, label]) => <button key={value} aria-pressed={historyFilter === value} onClick={() => setHistoryFilter(value)}>{label}</button>)}</div><span className="history-result-count">{filteredHistory.length} {filteredHistory.length === 1 ? "registro" : "registros"}</span></section>
        {historyMonths.length ? <div className="history-months">{historyMonths.map(({ month, items }) => <section key={month} className="history-month" aria-label={`Movimentações de ${monthLabel(month)}`}><div className="history-month-heading"><h2>{monthLabel(month)}</h2><span>{items.length} {items.length === 1 ? "registro" : "registros"}</span></div><ol className="history-feed">{items.map(({ expense, event }) => { const Icon = icons[expense.snapshot.category]; return <li key={`${expense.snapshot.id}-${event.id}`}><button className="history-event" disabled={Boolean(expense.snapshot.deleted)} onClick={() => { navigate("expenses"); open(expense.snapshot.id); }}><span className={`history-category-icon ${expense.snapshot.category === "Mercado" ? "incoming" : ""}`}><Icon size={18}/></span><div className="history-feed-content"><div className="history-feed-title"><strong>{expense.snapshot.title}</strong>{expense.snapshot.deleted && <span className="badge">Excluída</span>}</div><p><strong>{person(event.actor)}</strong> · {event.action}</p></div><div className="history-value"><strong>{money(expense.snapshot.cents)}</strong><time dateTime={event.createdAt}>{dateLabel(event.createdAt)}</time></div></button></li>; })}</ol></section>)}</div> : <p className="empty-state">Nenhuma movimentação encontrada. Tente escolher outro período ou filtro.</p>}
      </section>}

      {screen === "expenses" && <>
      <section id="despesas" className="expenses" aria-labelledby="expenses-title">
        <div className="section-head"><div><h2 id="expenses-title">Despesas de {activeGroup?.name}</h2><p>Abra uma despesa para conferir como ela foi dividida.</p></div></div>
        {quickLaunches.length > 0 && <section className="quick-launch" aria-labelledby="quick-launch-title"><div><h3 id="quick-launch-title">Adicionar mais rápido</h3><p>Reaproveite os dados da última conta lançada.</p></div><div className="quick-launch-list">{quickLaunches.map(({ label, expense }) => { const Icon = icons[expense.snapshot.category]; return <button key={label} onClick={() => openQuickExpense(expense)}><span className="expense-icon"><Icon size={18}/></span><span><strong>{label}</strong><small>Usar {expense.snapshot.title}</small></span><Plus size={16}/></button>; })}</div></section>}
        <div className="filters" aria-label="Filtrar por categoria">{["Todas", ...categories].map((category) =>
          <button key={category} aria-pressed={filter === category} onClick={() => setFilter(category)}>{category}</button>
        )}</div>
        <div className="expense-tools"><div className="tool-selects"><label className="period-filter">Período
            <select value={period} onChange={(event) => setPeriod(event.target.value)}>
              <option value="latest">Mês mais recente</option>
              <option value="all">Todo o histórico</option>
              {availableMonths.map((month) => <option value={month} key={month}>{monthLabel(month)}</option>)}
            </select>
          </label><label className="period-filter">Ordenar
            <select value={expenseSort} onChange={(event) => setExpenseSort(event.target.value as ExpenseSort)}>
              <option value="recent">Mais recentes</option>
              <option value="oldest">Mais antigas</option>
              <option value="highest">Maior valor</option>
              <option value="lowest">Menor valor</option>
              <option value="pending">Pendências primeiro</option>
            </select>
          </label></div></div>
        <section className="report" aria-label="Resumo do período selecionado">
          <div><span>Resumo de {activePeriod === "all" || !activePeriod ? "todo o histórico" : monthLabel(activePeriod)}</span><strong>{money(periodTotal)}</strong><small>{periodExpenses.length} {periodExpenses.length === 1 ? "despesa" : "despesas"} lançadas</small></div>
          <div className="report-details"><ul>{categoryTotals.map((item) => <li key={item.category}><span>{item.category}</span><strong>{money(item.cents)}</strong></li>)}</ul><button className="secondary export-button" onClick={exportReport}><Download size={16}/>Exportar CSV</button></div>
        </section>
        <div className="list">
          {shown.length === 0 && <p className="empty">Nenhuma despesa encontrada com estes filtros.</p>}
          {shown.map((expense) => {
            const item = expense.snapshot;
            const Icon = icons[item.category];
            return <button className="expense-row" key={item.id} disabled={!ready} onClick={() => open(item.id)}>
              <span className={`expense-icon ${item.category === "Mercado" ? "incoming" : ""}`}><Icon size={20}/></span>
              <span className="expense-info"><strong>{item.title}</strong><small>{item.category} · {dateLabel(item.date)}</small></span>
              {item.dispute && <span className="badge">{item.dispute.status === "resolved" ? "Resolvida" : "Em revisão"}</span>}
              <span className="expense-price"><strong>{money(item.cents)}</strong><small>{item.payer === "voce" ? "Você pagou" : `Pago por ${person(item.payer)}`}</small></span>
              <ChevronRight className="chevron" size={17}/>
            </button>;
          })}
        </div>
      </section>
      {deletedExpenses.length > 0 && <section className="audit-log" aria-labelledby="audit-title"><h2 id="audit-title">Registro de exclusões</h2><p>Estas despesas saíram da visão do grupo, mas podem ser restauradas quando a exclusão foi um engano.</p><ul>{deletedExpenses.map((expense) => <li key={expense.snapshot.id}><span><strong>{expense.snapshot.title}</strong><small>Excluída por {person(expense.snapshot.deleted!.actor)} em {eventDateLabel(expense.snapshot.deleted!.createdAt)}</small></span><div className="audit-actions"><strong>{money(expense.snapshot.cents)}</strong><button className="secondary" onClick={() => restoreExpense(expense)}><RotateCcw size={16}/>Restaurar</button></div></li>)}</ul></section>}
      </>}
      <p className="notice" role="status">{notice}</p>
      {screen === "expenses" && <button className="primary floating-add" disabled={!ready} onClick={openNewExpense}><Plus size={19}/>Nova despesa</button>}
    </main>

    {modal === "new" && <Modal title={quickTemplate ? `Nova despesa · ${quickTemplate.title}` : "Nova despesa"} close={() => { if (!creating) open(null); }}>
      <form key={quickTemplate?.id ?? "manual"} onSubmit={create}>
        {quickTemplate && <p className="quick-form-note"><Zap size={16}/>Dados de <strong>{quickTemplate.title}</strong> foram preenchidos. Confira o valor e a data antes de salvar.</p>}
        <label>Descrição<input name="title" required maxLength={100} defaultValue={quickTemplate?.title} placeholder="Ex.: Compras da semana"/></label>
        <div className="fields">
          <label>Valor (R$)<input name="amount" inputMode="decimal" required defaultValue={quickTemplate ? (quickTemplate.cents / 100).toFixed(2).replace(".", ",") : undefined} placeholder="0,00"/></label>
          <label>Data<input type="date" name="date" required defaultValue={quickTemplate ? localToday() : undefined}/></label>
        </div>
        <div className="fields">
          <label>Categoria<select name="category" defaultValue={quickTemplate?.category}>{categories.map((category) => <option key={category}>{category}</option>)}</select></label>
          <label>Quem pagou?<select name="payer" defaultValue={quickTemplate?.payer}>{residents.map((item) => <option key={item.id} value={item.id}>{item.name}</option>)}</select></label>
        </div>
        <fieldset><legend>Participantes da despesa</legend><div className="checks">{residents.map((item) => <label key={item.id}><input type="checkbox" checked={selectedResidents.includes(item.id)} onChange={(event) => setSelectedResidents((current) => event.target.checked ? [...current, item.id] : current.filter((resident) => resident !== item.id))}/>{item.name}</label>)}</div></fieldset>
        <fieldset><legend>Como dividir?</legend><div className="checks"><label><input type="radio" name="splitMode" checked={splitMode === "equal"} onChange={() => setSplitMode("equal")}/>Igualmente</label><label><input type="radio" name="splitMode" checked={splitMode === "custom"} onChange={() => setSplitMode("custom")}/>Valores diferentes</label></div></fieldset>
        <label className="recurrence-option"><input type="checkbox" name="recurring" defaultChecked={Boolean(quickTemplate?.recurrence)}/><span><Repeat2 size={17}/>Repetir mensalmente<small>Você poderá lançar a próxima mensalidade com um clique.</small></span></label>
        {splitMode === "custom" && <div className="custom-shares"><p>Informe o valor que cada pessoa deve pagar. A soma deve ser igual ao valor total.</p>{selectedResidents.map((resident) => <label key={resident}>{person(resident)}<input name={`share-${resident}`} inputMode="decimal" required defaultValue={quickTemplate?.customShares?.[resident] ? (quickTemplate.customShares[resident]! / 100).toFixed(2).replace(".", ",") : undefined} placeholder="0,00"/></label>)}</div>}
        <label>Comprovante (opcional, até 1 MB)<input name="receipt" type="file" accept="image/png,image/jpeg,image/webp,application/pdf"/></label>
        {error && <p className="error" role="alert">{error}</p>}
        <button className="primary" type="submit" disabled={creating}>{creating ? "Salvando…" : "Salvar despesa"}</button>
      </form>
    </Modal>}

    {groupPanelOpen && <Modal title="Grupos e moradores" close={() => setGroupPanelOpen(false)}>
      <p className="detail-meta">Escolha a moradia que você quer visualizar ou crie uma nova.</p>
      <div className="group-list">
        {groups.map((group) => <button className={`group-card ${group.id === activeGroupId ? "selected" : ""}`} key={group.id} onClick={() => selectGroup(group.id)}>
          <span className="group-icon"><House size={18}/></span><span><strong>{group.name}</strong><small>{group.residents.join(" · ")}</small></span>{group.id === activeGroupId && <span className="badge">Atual</span>}
        </button>)}
      </div>
      {activeGroup && <form className="add-member" onSubmit={addResidentToGroup}>
        <label>Adicionar usuário a {activeGroup.name}
          <select value={memberToAdd} onChange={(event) => setMemberToAdd(event.target.value)}>
            <option value="">Selecione um usuário</option>
            {users.filter((user) => !activeGroup.residents.includes(user.name)).map((user) => <option key={user.id} value={user.id}>{user.name} · {user.email}</option>)}
          </select>
        </label>
        <button className="secondary" type="submit" disabled={!memberToAdd}><Users size={17}/>Adicionar</button>
      </form>}
      {activeGroup && <section className="invite-card" aria-labelledby="invite-title"><div><h3 id="invite-title">Convidar para o grupo</h3><p>Compartilhe este convite com quem vai morar junto.</p></div><code>{inviteCode(activeGroup)}</code><button className="secondary" onClick={() => copyInvite(activeGroup)}><Copy size={17}/>{copiedInvite ? "Convite copiado" : "Copiar link e código"}</button><small>Convite de demonstração: a entrada real será ativada quando o banco de dados e o login estiverem conectados.</small></section>}
      {error && <p className="error" role="alert">{error}</p>}
      <div className="actions"><button className="primary" onClick={() => { setGroupPanelOpen(false); setGroupModalOpen(true); }}><Plus size={18}/>Criar grupo</button></div>
    </Modal>}

    {groupModalOpen && <Modal title="Criar grupo de moradia" close={() => setGroupModalOpen(false)}>
      <form onSubmit={createGroup}>
        <label>Nome do grupo<input name="groupName" required maxLength={60} placeholder="Ex.: Apartamento da Trindade"/></label>
        <label>Moradores separados por vírgula<textarea name="residents" required defaultValue="Você" maxLength={300} placeholder="Você, Ana, Léo"/></label>
        <p className="form-help">Os moradores serão visíveis neste grupo. Convites por link e e-mail serão incluídos depois.</p>
        {error && <p className="error" role="alert">{error}</p>}
        <button className="primary" type="submit">Criar grupo</button>
      </form>
    </Modal>}

    {reminderSettingsOpen && <Modal title="Lembretes de pagamento" close={() => setReminderSettingsOpen(false)}>
      <form onSubmit={saveReminderSettings}>
        <p className="form-help">Escolha quando quer receber avisos de pagamentos pendentes. Nesta demonstração, os avisos são enviados pelo navegador enquanto ele estiver aberto.</p>
        <fieldset className="reminder-options"><legend>Quando avisar</legend>{(Object.keys(reminderTimingLabels) as ReminderTiming[]).map((timing) => <label key={timing}><input type="radio" name="reminder-timing" value={timing} checked={reminderTiming === timing} onChange={() => setReminderTiming(timing)}/><span>{reminderTimingLabels[timing]}</span></label>)}</fieldset>
        {error && <p className="error" role="alert">{error}</p>}
        <div className="actions"><button className="secondary" type="button" onClick={enableReminders}><Bell size={17}/>{remindersEnabled ? "Enviar avisos atuais" : "Permitir notificações"}</button><button className="primary" type="submit">Salvar preferência</button></div>
      </form>
    </Modal>}

    {selected && <Modal title={selected.snapshot.title} close={() => open(null)}>
      {editing ? <form className="edit-form" onSubmit={(event) => {
        event.preventDefault();
        const form = new FormData(event.currentTarget);
        const saved = update(() => selected.edit("voce", {
          title: String(form.get("title")).trim(),
          cents: selected.snapshot.splitMode === "custom" ? selected.snapshot.cents : parseCents(String(form.get("amount"))),
          category: form.get("category") as Category,
          date: String(form.get("date")),
          payer: form.get("payer") as ResidentId,
        }), "Despesa atualizada.");
        if (saved) setEditing(false);
      }}>
        <label>Descrição<input name="title" required maxLength={100} defaultValue={selected.snapshot.title}/></label>
        <div className="fields">
          <label>Valor (R$)<input name="amount" inputMode="decimal" required disabled={selected.snapshot.splitMode === "custom"} defaultValue={(selected.snapshot.cents / 100).toFixed(2).replace(".", ",")}/></label>
          <label>Data<input name="date" type="date" required defaultValue={selected.snapshot.date}/></label>
        </div>
        {selected.snapshot.splitMode === "custom" && <p className="form-help">O valor fica bloqueado porque esta despesa usa divisão personalizada.</p>}
        <div className="fields">
          <label>Categoria<select name="category" defaultValue={selected.snapshot.category}>{categories.map((category) => <option key={category}>{category}</option>)}</select></label>
          <label>Quem pagou?<select name="payer" defaultValue={selected.snapshot.payer}>{residents.map((resident) => <option key={resident.id} value={resident.id}>{resident.name}</option>)}</select></label>
        </div>
        {error && <p className="error" role="alert">{error}</p>}
        <div className="actions"><button className="secondary" type="button" onClick={() => setEditing(false)}>Cancelar</button><button className="primary" type="submit">Salvar alterações</button></div>
      </form> : <>
      <p className="detail-meta">{selected.snapshot.category} · {dateLabel(selected.snapshot.date)} · Pago por {selected.snapshot.payer === "voce" ? "você" : person(selected.snapshot.payer)}{selected.snapshot.recurrence ? " · Recorrente mensal" : ""}</p>
      <div className="detail-total"><span>Valor total</span><strong>{money(selected.snapshot.cents)}</strong></div>
      <div className="actions"><button className="secondary" onClick={() => setEditing(true)}>Editar despesa</button><button className="danger-button" onClick={() => setDeleting(true)}>Excluir despesa</button></div>
      {deleting && <section className="delete-confirm" aria-labelledby="delete-title"><h3 id="delete-title">Excluir esta despesa?</h3><p>A despesa sai da visão do grupo, mas a exclusão ficará registrada para auditoria.</p><div className="actions"><button className="secondary" onClick={() => setDeleting(false)}>Cancelar</button><button className="danger-button" onClick={() => deleteExpense(selected)}>Sim, excluir</button></div></section>}
      <h3>Divisão por morador</h3>
      <p className="split-note">{selected.snapshot.splitMode === "custom" ? "Valores definidos individualmente" : "Divisão igual entre os participantes"}</p>
      <section className="calculation-card" aria-label="Como o valor foi calculado">
        <div className="calculation-heading"><span className="metric-icon"><Calculator size={19}/></span><div><strong>Como calculamos</strong><small>{selected.snapshot.splitMode === "custom" ? "Cada valor foi definido manualmente." : "A conta foi dividida igualmente entre os participantes."}</small></div></div>
        {selected.snapshot.splitMode === "custom" ? <div className="custom-calculation"><strong>{money(selected.snapshot.cents)}</strong><span>distribuídos conforme os valores abaixo</span></div> : <div className="calculation-equation"><strong>{money(selected.snapshot.cents)}</strong><span>÷</span><strong>{selected.snapshot.participants.length} {selected.snapshot.participants.length === 1 ? "morador" : "moradores"}</strong><span>=</span><strong>{money(selected.share(selected.snapshot.participants[0]))} por pessoa</strong></div>}
        {equalSplitHasRemainder && <p className="calculation-note">Os centavos restantes foram distribuídos entre os primeiros participantes para que o total seja exato.</p>}
        <div className="participant-chips">{selected.snapshot.participants.map((resident) => <span key={resident}>{person(resident)}</span>)}</div>
      </section>
      <ul className="shares">{selected.snapshot.participants.map((id) => <li key={id}><span>{person(id)}{selected.snapshot.paid.includes(id) ? ` · ${selected.isPaymentConfirmed(id) ? "Pagamento confirmado" : "Aguardando confirmação"}` : ""}</span><strong>{money(selected.share(id))}</strong></li>)}</ul>
      {selected.pending("voce") > 0 && <section className="pix-payment" aria-labelledby="pix-title">
        <div className="pix-heading"><div><h3 id="pix-title">Pagar via Pix</h3><p>Para {person(selected.snapshot.payer)} · ambiente de demonstração</p></div><strong>{money(selected.pending("voce"))}</strong></div>
        <code>{pixDemoCode(selected)}</code>
        <button className="secondary" onClick={() => copyPix(selected)}><Copy size={17}/>{copiedPix ? "Código copiado" : "Copiar código Pix"}</button>
        <a className="whatsapp-share" href={whatsAppUrl(selected)} target="_blank" rel="noreferrer"><MessageCircle size={17}/>Compartilhar no WhatsApp</a>
        <p className="pix-help">O código serve apenas para testar a interface e não inicia um pagamento real.</p>
      </section>}
      {residentsToRemind.length > 0 && <section className="reminder-card" aria-labelledby="reminder-title">
        <div className="reminder-heading"><div><span className="eyebrow"><Bell size={14}/>Cobrança guiada</span><h3 id="reminder-title">{residentsToRemind.length} {residentsToRemind.length === 1 ? "morador ainda não pagou" : "moradores ainda não pagaram"}</h3><p>Envie uma cobrança objetiva, já com o valor individual e a referência da despesa.</p></div><Send size={20}/></div>
        <ul>{residentsToRemind.map((resident) => <li key={resident}><span><strong>{person(resident)}</strong><small>Deve {money(selected.share(resident))}</small></span><div><button className="secondary reminder-copy" onClick={() => copyReminder(selected, resident)}><Copy size={16}/>{copiedReminder === resident ? "Copiado" : "Copiar"}</button><a className="whatsapp-share reminder-whatsapp" href={reminderWhatsAppUrl(selected, resident)} target="_blank" rel="noreferrer"><MessageCircle size={16}/>WhatsApp</a></div></li>)}</ul>
      </section>}
      {selected.snapshot.recurrence && <section className="recurrence-card" aria-label="Despesa recorrente"><div><Repeat2 size={18}/><span><strong>Despesa recorrente mensal</strong><small>A próxima cópia terá os mesmos participantes e valores, sem os pagamentos e comprovantes.</small></span></div><button className="secondary" onClick={() => addNextMonthlyExpense(selected)}>Lançar próxima mensalidade</button></section>}
      {awaitingConfirmation && <section className="confirmation-card" aria-label="Confirmação de recebimento"><div><strong>Pagamento aguardando confirmação</strong><p>Você informou o pagamento. Agora {person(selected.snapshot.payer)} precisa confirmar o recebimento.</p></div><button className="primary" onClick={() => update(() => selected.confirmPayment(selected.snapshot.payer, "voce"), "Recebimento confirmado.")}>Simular confirmação de {person(selected.snapshot.payer)}</button></section>}
      {selected.snapshot.receipt && <a className="secondary" download="comprovante" href={selected.snapshot.receipt}>Baixar comprovante</a>}
      <section className="history" aria-labelledby="history-title">
        <h3 id="history-title">Histórico de alterações</h3>
        <ol>{[...selected.history].reverse().map((event) => <li key={event.id}><span><strong>{person(event.actor)}</strong> {event.action}</span><time dateTime={event.createdAt}>{eventDateLabel(event.createdAt)}</time></li>)}</ol>
      </section>
      {selected.snapshot.dispute && <section className="dispute">
        <div className="dispute-heading"><h3>Contestação {selected.snapshot.dispute.status === "resolved" ? "resolvida" : "da despesa"}</h3><span className={`dispute-status ${selected.snapshot.dispute.status}`}>{selected.snapshot.dispute.status === "open" ? "Aguardando resposta" : selected.snapshot.dispute.status === "reviewed" ? "Resposta disponível" : "Resolvida"}</span></div>
        <p><strong>{selected.snapshot.dispute.reason}</strong> · {selected.snapshot.dispute.description}</p>
        {selected.snapshot.dispute.evidence && <a className="evidence-link" download={selected.snapshot.dispute.evidenceName ?? "prova-contestacao"} href={selected.snapshot.dispute.evidence}><Paperclip size={16}/>Abrir prova anexada{selected.snapshot.dispute.evidenceName ? `: ${selected.snapshot.dispute.evidenceName}` : ""}</a>}
        {selected.snapshot.dispute.followUps?.map((comment, index) => <p key={index}><strong>Comentário adicional</strong> · {comment}</p>)}
        {selected.snapshot.dispute.reply && <p><strong>Resposta</strong> · {selected.snapshot.dispute.reply}</p>}
        {selected.snapshot.dispute.status === "open" && <form onSubmit={(event) => {
          event.preventDefault();
          const form = new FormData(event.currentTarget);
          update(() => selected.review(selected.snapshot.payer, String(form.get("reply")), parseCents(String(form.get("amount")))), "Resposta de demonstração registrada.");
        }}>
          <label>Resposta do responsável (simulação)<textarea name="reply" required maxLength={500}/></label>
          <label>Valor revisado (R$)<input name="amount" inputMode="decimal" required defaultValue={(selected.snapshot.cents / 100).toFixed(2).replace(".", ",")}/></label>
          <button className="secondary" type="submit">Simular resposta</button>
        </form>}
        {selected.snapshot.dispute.status === "reviewed" && <form onSubmit={(event) => {
          event.preventDefault();
          const form = new FormData(event.currentTarget);
          update(() => selected.requestFurtherReview("voce", String(form.get("comment"))), "Novo comentário registrado.");
        }}>
          <label>Ainda precisa de revisão?<textarea name="comment" required maxLength={500}/></label>
          <div className="actions">
            <button className="secondary" type="submit">Enviar comentário</button>
            <button className="primary" type="button" onClick={() => update(() => selected.resolve("voce"), "Contestação marcada como resolvida.")}>Marcar como resolvida</button>
          </div>
        </form>}
      </section>}
      {contesting && <form className="dispute" onSubmit={async (event) => {
        event.preventDefault();
        const form = new FormData(event.currentTarget);
        try {
          const proof = await readProof(form.get("evidence"));
          update(() => selected.contest("voce", String(form.get("reason")), String(form.get("description")), proof), "Contestação enviada.");
        } catch (failure) {
          setError(failure instanceof Error ? failure.message : "Não foi possível anexar a prova.");
        }
      }}>
        <label>Motivo<select name="reason">{["Valor incorreto", "Participante incorreto", "Divisão incorreta", "Despesa duplicada", "Outro"].map((reason) => <option key={reason}>{reason}</option>)}</select></label>
        <label>O que precisa ser revisto?<textarea name="description" required maxLength={500}/></label>
        <label>Prova ou comprovante <small>Opcional · PNG, JPG, WebP ou PDF de até 1 MB</small><input name="evidence" type="file" accept="image/png,image/jpeg,image/webp,application/pdf"/></label>
        <button className="primary" type="submit">Enviar contestação</button>
      </form>}
      {!contesting && selected.pending("voce") > 0 && <div className="actions">
        {!selected.snapshot.dispute && <button className="secondary" onClick={() => setContesting(true)}>Contestar despesa</button>}
        {(!selected.snapshot.dispute || selected.snapshot.dispute.status === "resolved") &&
          <button className="primary" onClick={() => update(() => selected.markPaid("voce"), "Pagamento registrado.")}>Registrar pagamento</button>}
      </div>}
      {error && <p className="error" role="alert">{error}</p>}
      </>}
    </Modal>}
  </div>;
}
