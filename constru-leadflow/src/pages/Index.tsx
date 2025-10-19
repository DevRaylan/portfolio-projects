import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { supabase } from "@/integrations/supabase/client";
import Header from "@/components/Header";
import Hero from "@/components/Hero";
import Features from "@/components/Features";
import StatsCards from "@/components/StatsCards";
import Filters from "@/components/Filters";
import LeadsTable from "@/components/LeadsTable";
import MapView from "@/components/MapView";
import { AddConstructionDialog } from "@/components/AddConstructionDialog";

const Index = () => {
  const navigate = useNavigate();
  const [isAuthenticated, setIsAuthenticated] = useState<boolean | null>(null);

  useEffect(() => {
    supabase.auth.getSession().then(({ data: { session } }) => {
      setIsAuthenticated(!!session);
      if (!session) {
        navigate("/auth");
      }
    });

    const {
      data: { subscription },
    } = supabase.auth.onAuthStateChange((_event, session) => {
      setIsAuthenticated(!!session);
      if (!session) {
        navigate("/auth");
      }
    });

    return () => subscription.unsubscribe();
  }, [navigate]);

  if (isAuthenticated === null) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
          <p className="mt-4 text-muted-foreground">Carregando...</p>
        </div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return null;
  }

  return (
    <div className="min-h-screen">
      <Header />
      <Hero />
      <Features />
      
      <main className="container mx-auto px-4 py-12">
        <div className="space-y-8">
          <div>
            <div className="flex justify-between items-center mb-6">
              <h2 className="text-3xl font-bold">Dashboard</h2>
              <AddConstructionDialog />
            </div>
            <StatsCards />
          </div>
          
          <Filters />
          
          <div className="grid gap-8 lg:grid-cols-2">
            <LeadsTable />
            <MapView />
          </div>
        </div>
      </main>
      
      <footer className="mt-20 border-t bg-muted/50 py-12">
        <div className="container mx-auto px-4">
          <div className="grid gap-8 md:grid-cols-3">
            <div>
              <h3 className="mb-4 text-lg font-semibold">ConstruLink</h3>
              <p className="text-sm text-muted-foreground">
                Inteligência comercial para o setor da construção civil.
                Mapeie obras, qualifique leads e cresça seus negócios.
              </p>
            </div>
            <div>
              <h3 className="mb-4 text-lg font-semibold">Recursos</h3>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li><a href="#" className="hover:text-primary">Mapeamento de Obras</a></li>
                <li><a href="#" className="hover:text-primary">Análise Preditiva</a></li>
                <li><a href="#" className="hover:text-primary">Integração CRM</a></li>
                <li><a href="#" className="hover:text-primary">API</a></li>
              </ul>
            </div>
            <div>
              <h3 className="mb-4 text-lg font-semibold">Empresa</h3>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li><a href="#" className="hover:text-primary">Sobre Nós</a></li>
                <li><a href="#" className="hover:text-primary">Contato</a></li>
                <li><a href="#" className="hover:text-primary">Preços</a></li>
                <li><a href="#" className="hover:text-primary">Documentação</a></li>
              </ul>
            </div>
          </div>
          <div className="mt-8 border-t pt-8 text-center text-sm text-muted-foreground">
            <p>© 2025 ConstruLink. Inteligência Comercial para Construção Civil. Todos os direitos reservados.</p>
          </div>
        </div>
      </footer>
    </div>
  );
};

export default Index;