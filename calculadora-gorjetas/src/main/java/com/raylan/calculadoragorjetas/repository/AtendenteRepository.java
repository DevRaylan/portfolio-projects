package com.raylan.calculadoragorjetas.repository;

import java.util.List;

import com.raylan.calculadoragorjetas.model.Atendente;
import org.springframework.data.jpa.repository.JpaRepository;

public interface AtendenteRepository extends JpaRepository<Atendente, Long> {
    List<Atendente> findByAtivoTrue();
}