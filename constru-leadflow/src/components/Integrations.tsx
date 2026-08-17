import { MessageCircle, Mail, Webhook, Building } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

const integrations = [
  {
    icon: Building,
    title: "CRM",
    description: "Envie leads qualificados automaticamente para o seu CRM favorito.",
  },
  {
    icon: MessageCircle,
    title: "WhatsApp Business",
    description: "Notifique sua equipe assim que uma obra de alta prioridade for cadastrada.",
  },
  {
    icon: Mail,
    title: "E-mail Marketing",
    description: "Adicione contatos de obras diretamente às suas campanhas de e-mail.",
  },
  {
    icon: Webhook,
    title: "Webhooks / API",
    description: "Receba eventos em tempo real sempre que um lead for criado ou atualizado.",
  },
];

const Integrations = () => {
  return (
    <div className="grid gap-6 md:grid-cols-2">
      {integrations.map((integration) => (
        <Card key={integration.title} className="shadow-soft">
          <CardContent className="flex items-start gap-4 p-6">
            <div className="rounded-lg bg-primary/10 p-3 text-primary">
              <integration.icon className="h-6 w-6" />
            </div>
            <div className="flex-1">
              <div className="mb-1 flex items-center gap-2">
                <h3 className="font-semibold">{integration.title}</h3>
                <Badge variant="secondary">Em breve</Badge>
              </div>
              <p className="text-sm text-muted-foreground">{integration.description}</p>
            </div>
          </CardContent>
        </Card>
      ))}
    </div>
  );
};

export default Integrations;
