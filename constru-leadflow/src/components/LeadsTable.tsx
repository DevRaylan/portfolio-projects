import { useEffect, useState } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { useToast } from "@/hooks/use-toast";
import { Download } from "lucide-react";
import { exportConstructionsToCSV } from "@/utils/exportCSV";
import type { FilterState } from "./Filters";

interface Construction {
  id: string;
  name: string;
  city: string;
  state: string;
  location: string;
  status: "high" | "medium" | "low";
  estimated_value: number | null;
  contact_name: string | null;
  contact_phone: string | null;
  contact_email: string | null;
  buyer_name: string | null;
  buyer_phone: string | null;
  buyer_email: string | null;
  created_at: string;
}

interface LeadsTableProps {
  filters?: FilterState;
}

const LeadsTable = ({ filters }: LeadsTableProps) => {
  const [constructions, setConstructions] = useState<Construction[]>([]);
  const [allConstructions, setAllConstructions] = useState<Construction[]>([]);
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();

  useEffect(() => {
    fetchConstructions();
  }, []);

  useEffect(() => {
    applyFilters();
  }, [filters, allConstructions]);

  const fetchConstructions = async () => {
    try {
      const { data, error } = await supabase
        .from("constructions")
        .select("*")
        .order("created_at", { ascending: false });

      if (error) throw error;
      const fetchedData = (data || []) as Construction[];
      setAllConstructions(fetchedData);
      setConstructions(fetchedData.slice(0, 10));
    } catch (error: any) {
      toast({
        title: "Erro ao carregar obras",
        description: error.message,
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const applyFilters = () => {
    if (!filters) {
      setConstructions(allConstructions.slice(0, 10));
      return;
    }

    let filtered = [...allConstructions];

    if (filters.search) {
      const searchLower = filters.search.toLowerCase();
      filtered = filtered.filter(
        (c) =>
          c.name.toLowerCase().includes(searchLower) ||
          c.city.toLowerCase().includes(searchLower)
      );
    }

    if (filters.status && filters.status !== "all") {
      filtered = filtered.filter((c) => c.status === filters.status);
    }

    if (filters.location && filters.location !== "all") {
      filtered = filtered.filter((c) =>
        c.city.toLowerCase().includes(filters.location)
      );
    }

    setConstructions(filtered.slice(0, 10));
  };

  const handleExport = () => {
    exportConstructionsToCSV(constructions);
    toast({
      title: "Exportação concluída!",
      description: "Os dados foram exportados para CSV com sucesso.",
    });
  };

  const getBadgeVariant = (priority: string) => {
    switch (priority) {
      case "high":
        return "success";
      case "medium":
        return "warning";
      case "low":
        return "info";
      default:
        return "default";
    }
  };

  return (
    <Card className="shadow-soft">
      <CardHeader>
        <div className="flex items-center justify-between">
          <CardTitle className="text-2xl">Leads Recentes</CardTitle>
          <Button onClick={handleExport} variant="outline" size="sm" className="gap-2">
            <Download className="h-4 w-4" />
            Exportar CSV
          </Button>
        </div>
      </CardHeader>
      <CardContent>
        {loading ? (
          <div className="text-center py-8">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
            <p className="mt-4 text-sm text-muted-foreground">Carregando obras...</p>
          </div>
        ) : constructions.length === 0 ? (
          <div className="text-center py-8">
            <p className="text-muted-foreground">Nenhuma obra cadastrada ainda.</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b">
                  <th className="py-3 px-4 text-left text-sm font-semibold">Obra</th>
                  <th className="py-3 px-4 text-left text-sm font-semibold">Localização</th>
                  <th className="py-3 px-4 text-left text-sm font-semibold">Data</th>
                  <th className="py-3 px-4 text-left text-sm font-semibold">Valor Est.</th>
                  <th className="py-3 px-4 text-left text-sm font-semibold">Prioridade</th>
                </tr>
              </thead>
              <tbody>
                {constructions.map((construction) => (
                  <tr key={construction.id} className="border-b last:border-0 hover:bg-muted/50">
                    <td className="py-3 px-4 font-medium">{construction.name}</td>
                    <td className="py-3 px-4 text-sm text-muted-foreground">
                      {construction.city}
                    </td>
                    <td className="py-3 px-4 text-sm text-muted-foreground">
                      {new Date(construction.created_at).toLocaleDateString("pt-BR")}
                    </td>
                    <td className="py-3 px-4 text-sm">
                      {construction.estimated_value
                        ? `R$ ${construction.estimated_value.toLocaleString("pt-BR")}`
                        : "-"}
                    </td>
                    <td className="py-3 px-4">
                      <Badge variant={getBadgeVariant(construction.status)}>
                        {construction.status === "high"
                          ? "Alta"
                          : construction.status === "medium"
                          ? "Média"
                          : "Baixa"}
                      </Badge>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </CardContent>
    </Card>
  );
};

export default LeadsTable;
