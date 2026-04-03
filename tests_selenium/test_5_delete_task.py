import pytest
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time

BASE_URL = "http://localhost/Task-Managerv2"

class TestDeleteTaskFlow:
    """ US5: Eliminación de Tareas """

    def test_08_eliminar_tarea_flujo_positivo(self, driver):
        """[Happy Path / Prueba Positiva] Eliminar la tarea mediante el modal interactivo de confirmación"""
        driver.get(f"{BASE_URL}/dashboard.php")
        time.sleep(1)
        
        delete_btn = WebDriverWait(driver, 5).until(
            EC.element_to_be_clickable((By.CSS_SELECTOR, "button[title='Eliminar']"))
        )
        time.sleep(1)
        delete_btn.click()
        
        # Debe abrir el modal
        confirm_btn = WebDriverWait(driver, 5).until(
            EC.element_to_be_clickable((By.ID, "confirmDeleteBtn"))
        )
        time.sleep(1)  # dar tiempo extra para que el evaluador vea la animación modal
        confirm_btn.click()
        
        WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CLASS_NAME, "table"))
        )
        time.sleep(1)
        assert "dashboard" in driver.current_url
