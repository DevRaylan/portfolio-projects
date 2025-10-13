import { Sparkles, Database, Shield, Zap } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";

const features = [
  {
    icon: Sparkles,
    title: "Inteligência Artificial",
    description: "Previsão de demanda e classificação automática de leads com machine learning",
  },
  {
    icon: Database,
    title: "Integração de Dados",
    description: "Coleta automática de dados de prefeituras, CREA e concessionárias",
  },
  {
    icon: Shield,
    title: "Análise Geoespacial",
    description: "Mapeamento preciso de obras em fase inicial com coordenadas GPS",
  },
  {
    icon: Zap,
    title: "Automação Completa",
    description: "Alertas em tempo real e integração direta com seu CRM",
  },
];

const Features = () => {
  return (
    <section className="bg-gradient-subtle py-20">
      <div className="container mx-auto px-4">
        <div className="mb-12 text-center">
          <h2 className="mb-4 text-3xl font-bold md:text-4xl">
            Por que escolher o ConstruLink?
          </h2>
          <p className="mx-auto max-w-2xl text-lg text-muted-foreground">
            Tecnologia de ponta para identificar oportunidades antes da concorrência
          </p>
        </div>

        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
          {features.map((feature, index) => (
            <Card 
              key={index} 
              className="group border-2 shadow-soft transition-all duration-300 hover:border-primary hover:shadow-medium"
            >
              <CardContent className="p-6">
                <div className="mb-4 inline-flex rounded-lg bg-primary/10 p-3 text-primary transition-transform duration-300 group-hover:scale-110">
                  <feature.icon className="h-6 w-6" />
                </div>
                <h3 className="mb-2 text-lg font-semibold">{feature.title}</h3>
                <p className="text-sm text-muted-foreground">
                  {feature.description}
                </p>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Features;