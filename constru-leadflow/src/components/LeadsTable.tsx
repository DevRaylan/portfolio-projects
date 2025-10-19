import { useEffect, useState } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { useToast } from "@/hooks/use-toast";

interface Construction {
  id: string;
  name: string;
  city: string;
  status: "high" | "medium" | "low";
  estimated_value: number | null;
  created_at: string;
}

const LeadsTable = () => {
  const [constructions, setConstructions] = useState<Construction[]>([]);
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();

  useEffect(() => {
    fetchConstructions();
  }, []);

  const fetchConstructions = async () => {
    try {
      const { data, error } = await supabase
        .from("constructions")
        .select("id, name, city, status, estimated_value, created_at")
        .order("created_at", { ascending: false })
        .limit(10);

      if (error) throw error;
      setConstructions((data || []) as Construction[]);
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
        <CardTitle className="text-2xl">Leads Recentes</CardTitle>
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
