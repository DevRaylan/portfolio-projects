import { Building2, TrendingUp, Users, Target } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";

const stats = [
  {
    icon: Building2,
    value: "1.247",
    label: "Obras Ativas",
    trend: "+12%",
    color: "text-primary",
  },
  {
    icon: Target,
    value: "856",
    label: "Leads Qualificados",
    trend: "+24%",
    color: "text-accent",
  },
  {
    icon: TrendingUp,
    value: "68%",
    label: "Taxa de Conversão",
    trend: "+8%",
    color: "text-success",
  },
  {
    icon: Users,
    value: "342",
    label: "Clientes Ativos",
    trend: "+15%",
    color: "text-info",
  },
];

const StatsCards = () => {
  return (
    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
      {stats.map((stat, index) => (
        <Card key={index} className="overflow-hidden shadow-soft transition-all hover:shadow-medium">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div className="flex-1">
                <p className="text-sm text-muted-foreground">{stat.label}</p>
                <p className="mt-2 text-3xl font-bold">{stat.value}</p>
                <p className="mt-1 text-sm font-medium text-success">
                  {stat.trend} vs mês anterior
                </p>
              </div>
              <div className={`rounded-lg bg-secondary p-3 ${stat.color}`}>
                <stat.icon className="h-6 w-6" />
              </div>
            </div>
          </CardContent>
        </Card>
      ))}
    </div>
  );
};

export default StatsCards;