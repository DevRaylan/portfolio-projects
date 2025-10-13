import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { MapPin, Calendar, DollarSign } from "lucide-react";

interface Lead {
  id: number;
  name: string;
  location: string;
  status: "high" | "medium" | "low";
  date: string;
  value: string;
}

const mockLeads: Lead[] = [
  {
    id: 1,
    name: "Condomínio Residencial Aurora",
    location: "São Paulo, SP",
    status: "high",
    date: "15/01/2025",
    value: "R$ 245.000",
  },
  {
    id: 2,
    name: "Edifício Comercial Platina",
    location: "Curitiba, PR",
    status: "high",
    date: "12/01/2025",
    value: "R$ 180.000",
  },
  {
    id: 3,
    name: "Shopping Center Norte",
    location: "Porto Alegre, RS",
    status: "medium",
    date: "10/01/2025",
    value: "R$ 425.000",
  },
  {
    id: 4,
    name: "Residencial Parque das Flores",
    location: "Florianópolis, SC",
    status: "medium",
    date: "08/01/2025",
    value: "R$ 95.000",
  },
  {
    id: 5,
    name: "Galpão Industrial TechPark",
    location: "Blumenau, SC",
    status: "low",
    date: "05/01/2025",
    value: "R$ 62.000",
  },
];

const statusConfig = {
  high: { label: "Alta Prioridade", variant: "default" as const },
  medium: { label: "Média Prioridade", variant: "secondary" as const },
  low: { label: "Baixa Prioridade", variant: "outline" as const },
};

const LeadsTable = () => {
  return (
    <Card className="shadow-soft">
      <CardHeader>
        <CardTitle className="text-2xl">Leads Recentes</CardTitle>
      </CardHeader>
      <CardContent>
        <div className="space-y-4">
          {mockLeads.map((lead) => (
            <div
              key={lead.id}
              className="flex flex-col gap-3 rounded-lg border p-4 transition-colors hover:bg-muted/50 md:flex-row md:items-center md:justify-between"
            >
              <div className="flex-1">
                <h3 className="font-semibold">{lead.name}</h3>
                <div className="mt-2 flex flex-wrap gap-3 text-sm text-muted-foreground">
                  <div className="flex items-center gap-1">
                    <MapPin className="h-4 w-4" />
                    {lead.location}
                  </div>
                  <div className="flex items-center gap-1">
                    <Calendar className="h-4 w-4" />
                    {lead.date}
                  </div>
                  <div className="flex items-center gap-1">
                    <DollarSign className="h-4 w-4" />
                    {lead.value}
                  </div>
                </div>
              </div>
              <Badge variant={statusConfig[lead.status].variant}>
                {statusConfig[lead.status].label}
              </Badge>
            </div>
          ))}
        </div>
      </CardContent>
    </Card>
  );
};

export default LeadsTable;