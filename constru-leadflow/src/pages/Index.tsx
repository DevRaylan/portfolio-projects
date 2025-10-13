import { useState } from "react";
import Header from "@/components/Header";
import Hero from "@/components/Hero";
import Features from "@/components/Features";
import StatsCards from "@/components/StatsCards";
import Filters from "@/components/Filters";
import LeadsTable from "@/components/LeadsTable";
import MapView from "@/components/MapView";

const Index = () => {
  return (
    <div className="min-h-screen">
      <Header />
      <Hero />
      <Features />
      
      <main className="container mx-auto px-4 py-12">
        <div className="space-y-8">
          <div>
            <h2 className="mb-6 text-3xl font-bold">Dashboard</h2>
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