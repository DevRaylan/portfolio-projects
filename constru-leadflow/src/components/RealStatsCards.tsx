import { Building2, TrendingUp, Users, Target } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
import { useConstructions } from "@/hooks/useConstructions";
import { useCompanies } from "@/hooks/useCompanies";

const RealStatsCards = () => {
  const { data: constructions, isLoading: loadingConstructions, isError: constructionsError } = useConstructions();
  const { data: companies, isLoading: loadingCompanies, isError: companiesError } = useCompanies();

  const loading = loadingConstructions || loadingCompanies;
  const hasError = constructionsError || companiesError;

  const totalConstructions = constructions?.length ?? 0;
  const highPriority = constructions?.filter((c) => c.status === "high").length ?? 0;
  const totalValue = constructions?.reduce((sum, c) => sum + (c.estimated_value ?? 0), 0) ?? 0;
  const companiesCount = companies?.length ?? 0;

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat("pt-BR", {
      style: "currency",
      currency: "BRL",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(value);
  };

  const valueFor = (raw: string) => {
    if (loading) return "...";
    if (hasError) return "-";
    return raw;
  };

  const statsData = [
    {
      icon: Building2,
      value: valueFor(totalConstructions.toString()),
      label: "Obras Cadastradas",
      color: "text-primary",
    },
    {
      icon: Target,
      value: valueFor(highPriority.toString()),
      label: "Leads Alta Prioridade",
      color: "text-accent",
    },
    {
      icon: TrendingUp,
      value: valueFor(formatCurrency(totalValue)),
      label: "Valor Total Estimado",
      color: "text-success",
    },
    {
      icon: Users,
      value: valueFor(companiesCount.toString()),
      label: "Empresas Cadastradas",
      color: "text-info",
    },
  ];

  return (
    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
      {hasError && (
        <p className="col-span-full text-sm text-destructive">
          Não foi possível carregar as estatísticas. Tente recarregar a página.
        </p>
      )}
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
