import {
  Bar,
  BarChart,
  CartesianGrid,
  Cell,
  Pie,
  PieChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { useConstructions } from "@/hooks/useConstructions";

const STATUS_LABELS: Record<string, string> = {
  high: "Alta",
  medium: "Média",
  low: "Baixa",
};

const STATUS_COLORS: Record<string, string> = {
  high: "hsl(var(--destructive))",
  medium: "hsl(var(--accent))",
  low: "hsl(var(--muted-foreground))",
};

const Analytics = () => {
  const { data: rows = [], isLoading: loading, isError } = useConstructions();

  const byStatus = ["high", "medium", "low"].map((status) => ({
    status,
    label: STATUS_LABELS[status],
    count: rows.filter((r) => r.status === status).length,
    fill: STATUS_COLORS[status],
  }));

  const byCity = Object.entries(
    rows.reduce<Record<string, number>>((acc, r) => {
      acc[r.city] = (acc[r.city] ?? 0) + 1;
      return acc;
    }, {})
  )
    .map(([city, count]) => ({ city, count }))
    .sort((a, b) => b.count - a.count);

  if (loading) {
    return <p className="text-muted-foreground">Carregando analytics...</p>;
  }

  if (isError) {
    return <p className="text-destructive">Erro ao carregar analytics. Tente recarregar a página.</p>;
  }

  if (rows.length === 0) {
    return <p className="text-muted-foreground">Nenhuma obra cadastrada ainda.</p>;
  }

  return (
    <div className="grid gap-6 lg:grid-cols-2">
      <Card className="shadow-soft">
        <CardHeader>
          <CardTitle>Leads por Prioridade</CardTitle>
        </CardHeader>
        <CardContent>
          <ResponsiveContainer width="100%" height={280}>
            <PieChart>
              <Pie
                data={byStatus}
                dataKey="count"
                nameKey="label"
                cx="50%"
                cy="50%"
                outerRadius={90}
                label={(entry) => `${entry.label}: ${entry.count}`}
              >
                {byStatus.map((entry) => (
                  <Cell key={entry.status} fill={entry.fill} />
                ))}
              </Pie>
              <Tooltip />
            </PieChart>
          </ResponsiveContainer>
        </CardContent>
      </Card>

      <Card className="shadow-soft">
        <CardHeader>
          <CardTitle>Leads por Cidade</CardTitle>
        </CardHeader>
        <CardContent>
          <ResponsiveContainer width="100%" height={280}>
            <BarChart data={byCity}>
              <CartesianGrid strokeDasharray="3 3" className="stroke-border" />
              <XAxis dataKey="city" tick={{ fontSize: 12 }} />
              <YAxis allowDecimals={false} />
              <Tooltip />
              <Bar dataKey="count" fill="hsl(var(--primary))" radius={[4, 4, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        </CardContent>
      </Card>
    </div>
  );
};

export default Analytics;
