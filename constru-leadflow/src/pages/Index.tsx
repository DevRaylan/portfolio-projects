import Header from "@/components/Header";
import Hero from "@/components/Hero";
import StatsCards from "@/components/StatsCards";
import LeadsTable from "@/components/LeadsTable";
import MapView from "@/components/MapView";

const Index = () => {
  return (
    <div className="min-h-screen">
      <Header />
      <Hero />
      
      <main className="container mx-auto px-4 py-12">
        <div className="space-y-8">
          <div>
            <h2 className="mb-6 text-3xl font-bold">Dashboard</h2>
            <StatsCards />
          </div>
          
          <div className="grid gap-8 lg:grid-cols-2">
            <LeadsTable />
            <MapView />
          </div>
        </div>
      </main>
      
      <footer className="mt-20 border-t bg-muted/50 py-8">
        <div className="container mx-auto px-4 text-center text-sm text-muted-foreground">
          <p>© 2025 ConstruLink. Inteligência Comercial para Construção Civil.</p>
        </div>
      </footer>
    </div>
  );
};

export default Index;