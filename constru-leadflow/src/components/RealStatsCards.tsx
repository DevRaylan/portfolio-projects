import { useEffect, useState } from "react";
import { Building2, TrendingUp, Users, Target } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
import { supabase } from "@/integrations/supabase/client";

interface Stats {
  totalConstructions: number;
  highPriority: number;
  totalValue: number;
  companiesCount: number;
}

const RealStatsCards = () => {
  const [stats, setStats] = useState<Stats>({
    totalConstructions: 0,
    highPriority: 0,
    totalValue: 0,
    companiesCount: 0,
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      // Get constructions count and value
      const { data: constructions, error: constructionsError } = await supabase
        .from("constructions")
        .select("status, estimated_value");

      if (constructionsError) throw constructionsError;

      // Get companies count
      const { data: companies, error: companiesError } = await supabase
        .from("companies")
        .select("id");

      if (companiesError) throw companiesError;

      const totalConstructions = constructions?.length || 0;
      const highPriority = constructions?.filter((c) => c.status === "high").length || 0;
      const totalValue = constructions?.reduce((sum, c) => sum + (c.estimated_value || 0), 0) || 0;
      const companiesCount = companies?.length || 0;

      setStats({
        totalConstructions,
        highPriority,
        totalValue,
        companiesCount,
      });
    } catch (error) {
      console.error("Erro ao carregar estatísticas:", error);
    } finally {
      setLoading(false);
    }
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat("pt-BR", {
      style: "currency",
      currency: "BRL",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(value);
  };

  const statsData = [
    {
      icon: Building2,
      value: loading ? "..." : stats.totalConstructions.toString(),
      label: "Obras Cadastradas",
      color: "text-primary",
    },
    {
      icon: Target,
      value: loading ? "..." : stats.highPriority.toString(),
      label: "Leads Alta Prioridade",
      color: "text-accent",
    },
    {
      icon: TrendingUp,
      value: loading ? "..." : formatCurrency(stats.totalValue),
      label: "Valor Total Estimado",
      color: "text-success",
    },
    {
      icon: Users,
      value: loading ? "..." : stats.companiesCount.toString(),
      label: "Empresas Cadastradas",
      color: "text-info",
    },
  ];

  return (
    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
      {statsData.map((stat, index) => (
        <Card key={index} className="overflow-hidden shadow-soft transition-all hover:shadow-medium">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div className="flex-1">
                <p className="text-sm text-muted-foreground">{stat.label}</p>
                <p className="mt-2 text-3xl font-bold">{stat.value}</p>
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

export default RealStatsCards;
