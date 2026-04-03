import pytest
from selenium.webdriver.common.by import By
import time

BASE_URL = "http://localhost/Task-Managerv2"

class TestReadTasksFlow:
    """ US3: Leer y Listar Tareas en el Dashboard """

    def test_05_leer_dashboard_flujo_positivo(self, driver):
        """[Happy Path / Prueba Positiva] Cargar las tareas y ver componentes principales visuales"""
        driver.get(f"{BASE_URL}/dashboard.php")
        time.sleep(1.5) # Dejar visualmente claro que estamos escaneando la tabla
        
        # Verificar componente Panel Bento de estadísticas
        stat_total = driver.find_element(By.CLASS_NAME, "stat-total")
        assert stat_total.is_displayed(), "El cuadro de métricas total debe mostrarse."
        time.sleep(1)
        
        # Verificar la estructura de la tabla HTML de tareas
        table = driver.find_element(By.CLASS_NAME, "table")
        assert table.is_displayed(), "La tabla principal debe ser visible en el dashboard."
        time.sleep(1)
