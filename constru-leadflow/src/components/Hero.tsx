import { ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import heroImage from "@/assets/hero-construction.jpg";

const Hero = () => {
  return (
    <section className="relative overflow-hidden bg-gradient-subtle py-20 md:py-32">
      <div className="absolute inset-0 -z-10 opacity-10">
        <img 
          src={heroImage} 
          alt="Construção com dados analíticos" 
          className="h-full w-full object-cover"
        />
      </div>
      
      <div className="container mx-auto px-4">
        <div className="mx-auto max-w-4xl text-center">
          <div className="mb-6 inline-block rounded-full bg-primary/10 px-4 py-2 text-sm font-medium text-primary">
            Inteligência Comercial para Construção Civil
          </div>
          
          <h1 className="mb-6 text-4xl font-bold tracking-tight md:text-6xl lg:text-7xl">
            Transforme dados de obras em{" "}
            <span className="bg-gradient-primary bg-clip-text text-transparent">
              oportunidades de negócio
            </span>
          </h1>
          
          <p className="mb-10 text-lg text-muted-foreground md:text-xl">
            Mapeie obras em fase inicial, preveja demanda e priorize os melhores leads 
            para sua empresa com inteligência artificial e análise geoespacial.
          </p>
          
          <div className="flex flex-col gap-4 sm:flex-row sm:justify-center">
            <Button size="lg" className="gap-2 shadow-medium transition-all hover:shadow-strong">
              Começar Agora
              <ArrowRight className="h-4 w-4" />
            </Button>
            <Button size="lg" variant="outline">
              Ver Demonstração
            </Button>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Hero;