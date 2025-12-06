@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; text-align: center; padding: 40px 20px;">
    
    <!-- Logo & Title -->
    <div style="margin-bottom: 30px; animation: fadeInDown 0.6s ease-out;">
        <h1 style="font-size: 48px; margin-bottom: 10px;">Meal Metrics</h1>
        <p style="font-size: 18px; color: #555; margin-bottom: 20px;">
            Track your nutrition, manage your recipes, and achieve your health goals.
        </p>
    </div>

    <!-- Feature Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;" id="cardsContainer">
        
        <!-- Card 1: Plan Meals -->
        <div class="feature-card" style="border: 1px solid #ddd; padding: 20px; text-align: left; background: #fafafa; cursor: pointer; transition: all 0.3s ease;">
            <div style="font-size: 40px; margin-bottom: 10px;">📅</div>
            <h3 style="margin-bottom: 10px; font-size: 20px;">Plan Meals</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                Create meal plans for your week. Schedule breakfast, lunch, dinner, and snacks with your favorite recipes or custom foods.
            </p>
        </div>

        <!-- Card 2: Track Nutrition -->
        <div class="feature-card" style="border: 1px solid #ddd; padding: 20px; text-align: left; background: #fafafa; cursor: pointer; transition: all 0.3s ease;">
            <div style="font-size: 40px; margin-bottom: 10px;">📊</div>
            <h3 style="margin-bottom: 10px; font-size: 20px;">Track Nutrition</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                Log your daily food intake and watch your nutrition totals. Monitor calories, protein, carbs, fat, fiber, and more.
            </p>
        </div>

        <!-- Card 3: Set Goals -->
        <div class="feature-card" style="border: 1px solid #ddd; padding: 20px; text-align: left; background: #fafafa; cursor: pointer; transition: all 0.3s ease;">
            <div style="font-size: 40px; margin-bottom: 10px;">🎯</div>
            <h3 style="margin-bottom: 10px; font-size: 20px;">Set Goals</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                Define your nutrition goals. Set targets for calories, protein, fiber, and more. Track your progress toward your goals.
            </p>
        </div>

        <!-- Card 4: Manage Recipes -->
        <div class="feature-card" style="border: 1px solid #ddd; padding: 20px; text-align: left; background: #fafafa; cursor: pointer; transition: all 0.3s ease;">
            <div style="font-size: 40px; margin-bottom: 10px;">🍽️</div>
            <h3 style="margin-bottom: 10px; font-size: 20px;">Create Recipes</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                Build your personal recipe collection. Add ingredients, track nutrition facts, and calculate carbon footprint per serving.
            </p>
        </div>

        <!-- Card 5: Track Health -->
        <div class="feature-card" style="border: 1px solid #ddd; padding: 20px; text-align: left; background: #fafafa; cursor: pointer; transition: all 0.3s ease;">
            <div style="font-size: 40px; margin-bottom: 10px;">❤️</div>
            <h3 style="margin-bottom: 10px; font-size: 20px;">Track Health</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                Log your weight and blood pressure. View 30-day averages and track your health metrics over time.
            </p>
        </div>

        <!-- Card 6: Carbon Footprint -->
        <div class="feature-card" style="border: 1px solid #ddd; padding: 20px; text-align: left; background: #fafafa; cursor: pointer; transition: all 0.3s ease;">
            <div style="font-size: 40px; margin-bottom: 10px;">🌱</div>
            <h3 style="margin-bottom: 10px; font-size: 20px;">Carbon Footprint</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                Track the environmental impact of your food choices. Make informed decisions to reduce your carbon footprint while eating well.
            </p>
        </div>

    </div>

    <!-- Call to Action -->
    <div style="background: #f0f0f0; padding: 40px; border-radius: 8px; animation: fadeInUp 0.6s ease-out 0.2s backwards;">
        <h2 style="margin-bottom: 20px; font-size: 28px;">Ready to Get Started?</h2>
        <p style="color: #666; margin-bottom: 30px; font-size: 16px;">
            Create an account or log in to start tracking your nutrition and managing your health goals today.
        </p>
        <a href="{{ route('login.form') }}" id="ctaButton" style="background: #222; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; transition: all 0.3s ease; cursor: pointer;">
            Get Started
        </a>
    </div>

</div>

<style>
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .feature-card {
        animation: slideInLeft 0.5s ease-out forwards;
        opacity: 0;
    }

    .feature-card:nth-child(1) { animation-delay: 0.1s; }
    .feature-card:nth-child(2) { animation-delay: 0.2s; }
    .feature-card:nth-child(3) { animation-delay: 0.3s; }
    .feature-card:nth-child(4) { animation-delay: 0.4s; }
    .feature-card:nth-child(5) { animation-delay: 0.5s; }
    .feature-card:nth-child(6) { animation-delay: 0.6s; }

    @media (max-width: 768px) {
        h1 { font-size: 36px; }
        p { font-size: 14px; }
    }
</style>

<script>
// Card hover effects
document.querySelectorAll('.feature-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
        this.style.backgroundColor = '#f5f5f5';
    });

    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'none';
        this.style.backgroundColor = '#fafafa';
    });

    card.addEventListener('click', function() {
        this.style.transform = 'scale(0.98)';
        setTimeout(() => {
            this.style.transform = 'translateY(-5px)';
        }, 100);
    });
});

// CTA button effects
const ctaButton = document.getElementById('ctaButton');
if (ctaButton) {
    ctaButton.addEventListener('mouseenter', function() {
        this.style.backgroundColor = '#000';
        this.style.transform = 'translateY(-2px)';
        this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.2)';
    });

    ctaButton.addEventListener('mouseleave', function() {
        this.style.backgroundColor = '#222';
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'none';
    });

    ctaButton.addEventListener('click', function(e) {
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = 'translateY(-2px)';
        }, 100);
    });
}

// Smooth scroll for anchor links
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});

// Keyboard shortcuts: G to get started, Esc to scroll to top
document.addEventListener('keydown', function(e) {
    if ((e.key === 'g' || e.key === 'G') && ctaButton) {
        ctaButton.click();
    }
    if (e.key === 'Escape') {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

// Reset button state on page visibility change
document.addEventListener('visibilitychange', function() {
    if (!document.hidden && ctaButton) {
        ctaButton.style.backgroundColor = '#222';
        ctaButton.style.transform = 'translateY(0)';
        ctaButton.style.boxShadow = 'none';
    }
});
</script>
@endsection