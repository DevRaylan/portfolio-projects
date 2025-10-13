import { Building2, Menu } from "lucide-react";
import { Button } from "@/components/ui/button";

const Header = () => {
  return (
    <header className="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
      <div className="container mx-auto flex h-16 items-center justify-between px-4">
        <div className="flex items-center gap-2">
          <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-primary">
            <Building2 className="h-6 w-6 text-primary-foreground" />
          </div>
          <span className="text-xl font-bold">ConstruLink</span>
        </div>
        
        <nav className="hidden items-center gap-6 md:flex">
          <a href="#dashboard" className="text-sm font-medium transition-colors hover:text-primary">
            Dashboard
          </a>
          <a href="#leads" className="text-sm font-medium transition-colors hover:text-primary">
            Leads
          </a>
          <a href="#analytics" className="text-sm font-medium transition-colors hover:text-primary">
            Analytics
          </a>
          <a href="#integrations" className="text-sm font-medium transition-colors hover:text-primary">
            Integrações
          </a>
        </nav>
        
        <div className="flex items-center gap-4">
          <Button className="hidden md:inline-flex">
            Entrar
          </Button>
          <Button variant="ghost" size="icon" className="md:hidden">
            <Menu className="h-5 w-5" />
          </Button>
        </div>
      </div>
    </header>
  );
};

export default Header;