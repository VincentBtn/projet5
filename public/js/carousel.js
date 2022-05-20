class Diaporama {
  constructor(idDiaporama) {
      this.index = 0
      this.container = document.getElementById(idDiaporama);
      this.slides = this.container.querySelectorAll("figure");
      this.nextButton = this.container.querySelector(".diapo-next");
      this.previousButton = this.container.querySelector(".diapo-previous")
      this.playButton = this.container.querySelector(".play")
      this.stopButton = this.container.querySelector(".stop")
      this.refresh()
      this.nextButton.addEventListener("click", () => {
          this.next()

      })
      this.previousButton.addEventListener("click", () => {
          this.previous()
      })
      this.playButton.addEventListener("click", () => {
          this.intervalID = window.setInterval(() => {
              this.next()
          }, 5000);

      })
      this.intervalID = window.setInterval(() => {
          this.next()
      }, 5000);
      this.stopButton.addEventListener("click", () => {

          window.clearInterval(this.intervalID)
      })
      window.document.addEventListener("keydown", (e) => {
          if (e.keyCode === 37) this.previous()
          if (e.keyCode === 39) this.next()
      })
  }
  next() {
      this.index++;
      if (this.index >= this.slides.length) this.index = 0;
      this.refresh()
  }

  previous() {
      this.index--;
      if (this.index < 0) this.index = this.slides.length - 1;
      this.refresh()
  }

  refresh() {
      for (var i = 0; i < this.slides.length; i++) {
          this.slides[i].style.display = "none";
      }
      this.slides[this.index].style.display = "block";
  }
}

document.addEventListener('DOMContentLoaded', () => {
    const diaporama = new Diaporama("diaporama")
})
